let hamburger = document.querySelector('.hamburger');
let navLinks = document.querySelector('.nav-links');    
let links = document.querySelectorAll('.links');    

//Display links onClick on Hamburger
hamburger.addEventListener('click', ()=> {
    navLinks.classList.toggle('hide');
    hamburger.classList.toggle('lines-rotate');
});

//Hide navlink Container onClick any single link
for (let i = 0; i < links.length; i++) {
    links[i].addEventListener('click',() => {
        navLinks.classList.toggle('hide');
    });
}



