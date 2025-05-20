let menu =document.querySelector('#menu-icon');
let navbar =document.querySelector('.navbar');

menu.onclick =()=>{
    menu.classList.toggle('bx-x');
    navbar.classList.toggle('active');

}
window.onscroll =() =>{
    menu.classList.remove('bx-x');
    navbar.classList.remove('active');
}

/*typing text*/
const typed = new Typed('.multiple-text', {
    strings: ['لكمال الاجسام ', 'حقق حلمك الان','  واترك الباقي علينا','  فنجاحك هو هدفنا',' دع احلامك تتحقق مع' ,'القائد '],
    typeSpeed: 60,
    backspeed:60,
    backDelay:1000,
    loop:true,
  });