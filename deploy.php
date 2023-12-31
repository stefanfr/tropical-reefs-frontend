<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('http_user', 'nginx');
set('application', 'tropical-reefs-frontend');

// Project repository
set('repository', 'git@gitlab.hostplus.nl:tropical-reefs/frontend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('pty', true);
set('git_tty', false);
set('ssh_type', 'native');
set('ssh_multiplexing', true);

// Shared files/dirs between deploys
set('copy_dirs', ['vendor', 'node_modules']);
add('shared_dirs', ['public/media']);
add('shared_files', ['.env']);

// Writable dirs by web server
set('writable_mode', 'chown');
add('writable_dirs', ['var/cache', 'var/log', 'var/sessions', 'public/media']);

// Hosts
// Dev
host('192.168.40.111')
    ->stage('staging')
    ->user('root')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/vhosts/{{application}}');

// Tasks
task('build', static function () {
    run('cd {{release_path}} && build');
});

/**
 * Chowning
 */
task('deploy:permissions', static function () {
    run('cd {{release_path}}/../../ && chown -R {{http_user}}:{{http_user}} *');
    run('cd {{release_path}} && composer dump-autoload --optimize --classmap-authoritative');
})->desc('Chowning uploads');


/**
 * Statics
 */
task('deploy:statics', static function () {
    run('cd {{release_path}} && npm ci');
    run('cd {{release_path}} && npm run build');
})->desc('Deploying statics');

/**
 * UpdateDB
 */
task('deploy:database:update', static function () {
    run('cd {{release_path}} && php bin/console doctrine:schema:update --force --complete');
    run('cd {{release_path}} && php bin/console doctrine:schema:update --force --em=manager --complete');
})->desc('Updating database');

task('deploy:secrets', function () {
    upload(getenv('DOTENV'), '{{deploy_path}}/shared/.env');
});

task('deploy:supervisor', function () {
    run('supervisorctl restart all');
    run('systemctl restart php-fpm');
});

task('deploy:supervisor', function () {
    run('supervisorctl restart all');
});

task('deploy:restart:fpm', function () {
    run('systemctl restart php-fpm');
});

task('deploy:translations', function () {
    run('cd {{release_path}} && php bin/console app:localize:vue');
});

task('deploy:redis:flush', function () {
    run('redis-cli flushall');
});

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
//    'deploy:secrets',
    'deploy:copy_dirs',
    'deploy:clear_paths',
    'deploy:shared',
    'deploy:vendors',
    'deploy:statics',
    'deploy:cache:warmup',
    'deploy:writable',
    'deploy:database:update',
    'deploy:symlink',
    'deploy:permissions',
    'deploy:restart:fpm',
    'deploy:redis:flush',
//    'deploy:supervisor',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');

// Display success message on completion
after('deploy', 'success');
