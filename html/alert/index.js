const toast = document.querySelector(".toast");
(closeIcon = document.querySelector(".close")),
  (progress = document.querySelector(".progress"));

let timer1, timer2;

function getAlert(message, success){

  document.getElementsByClassName("toast-text-2")[0].innerHTML = message;

    if(success){
        document.getElementsByClassName("toast-check")[0].innerHTML = '&#10003';
        document.getElementsByClassName("toast-text-1")[0].innerHTML = 'Success';

        try {
            document.getElementsByClassName("toast-check")[0].classList.remove('toast-check-error');
            document.getElementsByClassName("toast-check")[0].classList.add('toast-check-success');
        } catch (error) {}

    }
    else{
        document.getElementsByClassName("toast-check")[0].innerHTML = 'X';
        document.getElementsByClassName("toast-text-1")[0].innerHTML = 'Error';

        try {
            document.getElementsByClassName("toast-check")[0].classList.remove('toast-check-success');
            document.getElementsByClassName("toast-check")[0].classList.add('toast-check-error');
        } catch (error) {}
    }

  toast.classList.add("active");
  progress.classList.add("active");

  timer1 = setTimeout(() => {
    toast.classList.remove("active");
  }, 5000); //1s = 1000 milliseconds

  timer2 = setTimeout(() => {
    progress.classList.remove("active");
  }, 5300);
};
