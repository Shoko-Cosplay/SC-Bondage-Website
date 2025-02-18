export class CaptachaForm extends HTMLElement{
  connectedCallback() {
    let input = document.createElement('input');
    input.name = this.getAttribute('field')+"[captacha]";
    input.type = "hidden";
    input.required = true;
    this.appendChild(input);

    let form = input.form;

    let vm = document.createElement('div');
    this.parentElement.appendChild(vm);

    let captcha = hcaptcha.render(this,{
      sitekey : this.getAttribute('key'),
      callback: function (e){
        input.value = e;
        input.setAttribute('value',e);
        form.submit();
      }
    })



    form.addEventListener('submit',(e)=>{
      e.preventDefault();
    })
  }
}
