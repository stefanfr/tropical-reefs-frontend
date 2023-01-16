import './styles/glide.scss';
import Glide, { Controls, Swipe } from '@glidejs/glide/dist/glide.modular.esm'

new Glide('.glide', {
    type: 'carousel',
    perView: 1
}).mount({ Controls, Swipe });

