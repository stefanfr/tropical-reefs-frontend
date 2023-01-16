import './styles/glide.scss';
import Glide  from '@glidejs/glide/dist/glide.modular.esm'

const ready = require('document-ready');

ready(function () {
    new Glide('.glide', {
        type: 'carousel',
        perView: 1
    }).mount();
})

