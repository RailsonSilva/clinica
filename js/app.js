const menu = document.querySelector('.menu');
const fecharMenu = document.querySelector('.fecharMenu');
const abrirMenu = document.querySelector('.abrirMenu');

abrirMenu.addEventListener('click', mostrar);
fecharMenu.addEventListener('click', fechar);

function mostrar(){
    menu.style.display = 'flex';
    menu.style.top = '0';
}

function fechar(){
    menu.style.top = '-100%';
}