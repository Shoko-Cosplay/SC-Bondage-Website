export class DisplayEmail {
  constructor() {
    let hCaptcha = window.shoko.h_captcha
    if (hCaptcha != "" && hCaptcha != undefined) {
      let displayAddress = document.querySelectorAll('.display-address');
      displayAddress.forEach(displayAddress => {
        displayAddress.addEventListener('click', (e) => {
          e.preventDefault();
          fetch("/api/displayAddress", () => {
          })
            .then(resp => resp.json())
            .then((rslt) => {
              if (rslt.email != undefined) {
                displayAddress.innerHTML = rslt.email;
              } else {
                displayAddress.innerHTML = "Erreur de chargement";
              }
            });
        })
      })
    }
  }
}
