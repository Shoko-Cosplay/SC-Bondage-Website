import Swiper from 'swiper';
// import Swiper styles
import 'swiper/css';

export class Swipper {
  constructor() {
    let swiperCtrl = document.querySelectorAll('.swiperCtrl');
    swiperCtrl.forEach(swiperCtrl=>{
      let swiper = new Swiper(swiperCtrl, {
        // Optional parameters
        direction: 'horizontal',
        loop: true,
        autoplay: {
          delay: 5000,
        }
      })
    })
  }
}
