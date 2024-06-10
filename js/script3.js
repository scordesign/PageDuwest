const botonAbrir = document.getElementById('abrir-carruselfotos');
const carrusel = document.getElementById('carruselfotos');
const botonAnterior = document.getElementById('anterior');
const botonSiguiente = document.getElementById('siguiente');

let indiceActual = 0;

botonAbrir.addEventListener('click', () => {
    carrusel.classList.remove('oculto');
});

botonAnterior.addEventListener('click', () => {
    indiceActual--;

    if (indiceActual < 0) {
        indiceActual = 2;
    }

    mostrarImagen(indiceActual);
});

botonSiguiente.addEventListener('click', () => {
    indiceActual++;

    if (indiceActual > 2) {
        indiceActual = 0;
    }

    mostrarImagen(indiceActual);
});

function mostrarImagen(indice) {
    const imagenes = document.querySelectorAll('.contenedor-imagenesfotos img');
    imagenes.forEach(imagen => imagen.style.display = 'none');

    imagenes[indice].style.display = 'block';
}