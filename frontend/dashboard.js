document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.container');

    container.addEventListener('click', function() {
        container.classList.toggle('expanded'); // Toggle the 'expanded' class
    });
});

