// script.js

// Ambil semua link di navbar dan semua elemen section
const navLinks = document.querySelectorAll('.navbar a');
const sections = document.querySelectorAll('section');

window.addEventListener('scroll', () => {
  let currentSectionId = '';

  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    
    // Angka 80 adalah kompensasi tinggi navbar agar perpindahan menu terasa pas
    if (window.scrollY >= (sectionTop - 80)) {
      currentSectionId = section.getAttribute('id');
    }
  });

  // Hapus kelas 'active' dari semua menu, lalu tambahkan ke menu yang aktif
  navLinks.forEach(link => {
    link.classList.remove('active');
    if (link.getAttribute('href') === `#${currentSectionId}`) {
      link.classList.add('active');
    }
  });
});