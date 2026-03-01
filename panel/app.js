const menuLinks = document.querySelectorAll('.menu a');

menuLinks.forEach((link) => {
  link.addEventListener('click', (event) => {
    event.preventDefault();
    menuLinks.forEach((item) => item.classList.remove('active'));
    link.classList.add('active');
  });
});
