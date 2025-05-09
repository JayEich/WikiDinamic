// Necesitas la BASE_URL de tu aplicación para construir las URLs de fetch
// Puedes definirla globalmente en window.APP.baseURL (como ya lo tienes)
// o pasarla explícitamente.
import { BASE_URL } from '../../shared/config.js'; // Ajusta la ruta si es diferente

document.addEventListener("DOMContentLoaded", function() {
    // Elementos principales
    const articleMenu = document.getElementById("articleMenu");
    const articleBody = document.getElementById("articleBody");
    const articleContent = document.getElementById("articleContent");

    // ===== FUNCIONALIDAD DEL TOGGLE DEL ASIDE =====
    setupAsideToggle();

    // ===== MANEJO DE ARTÍCULOS Y SUBTEMAS =====
    setupArticlesAndSubtopics();

    // ===== FUNCIONES DE IMPLEMENTACIÓN =====

    // Función para configurar el botón de toggle del aside
    function setupAsideToggle() {
        if (!document.querySelector(".toggle-aside-button")) {
            const toggleButton = document.createElement("button");
            toggleButton.className = "toggle-aside-button menu-visible";
            toggleButton.innerHTML = "☰";
            toggleButton.setAttribute("title", "Mostrar/Ocultar menú");
            document.body.appendChild(toggleButton);

            toggleButton.addEventListener("click", function() {
                const isHidden = articleMenu.classList.toggle("hidden-aside");
                this.innerHTML = isHidden ? "☰" : "✖";
                this.classList.toggle("menu-visible", !isHidden);
                articleContent.classList.toggle("content-expanded", isHidden);
                console.log(`Aside ${isHidden ? 'oculto' : 'visible'}, contenido ${isHidden ? 'expandido' : 'normal'}`);
            });
        }
    }

    // Función para configurar los eventos de artículos y subtemas
    function setupArticlesAndSubtopics() {
        const articleLinks = document.querySelectorAll(".article-link");
        articleLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                articleLinks.forEach(el => el.classList.remove("active"));
                this.classList.add("active");
                const articleId = this.dataset.id;
                loadArticle(articleId);
            });
        });

        const toggleButtons = document.querySelectorAll(".toggle-subtopics");
        toggleButtons.forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const targetId = this.dataset.target;
                const subtopicsContainer = document.getElementById(targetId);
                const isExpanded = subtopicsContainer.classList.toggle("expanded");
                this.classList.toggle("expanded", isExpanded);
                console.log(`Toggle subtopics for ${targetId}: ${isExpanded ? 'expanded' : 'collapsed'}`);
            });
        });

        const subtopicLinks = document.querySelectorAll(".subtopic-link");
        subtopicLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                const articleId = this.dataset.article;
                const targetId = this.dataset.target;
                const parentArticleLink = document.querySelector(`.article-link[data-id="${articleId}"]`);
                if (parentArticleLink && !parentArticleLink.classList.contains("active")) {
                    articleLinks.forEach(el => el.classList.remove("active"));
                    parentArticleLink.classList.add("active");
                    loadArticle(articleId, function() {
                        scrollToSubtopic(targetId);
                    });
                } else {
                    scrollToSubtopic(targetId);
                }
                subtopicLinks.forEach(el => el.classList.remove("active"));
                this.classList.add("active");
            });
        });
    }

    // Función para cargar un artículo
    function loadArticle(articleId, callback) {
        articleBody.innerHTML = `
            <div class="text-center my-5">
                <div class="loading-spinner"></div>
                <p class="mt-3">Cargando artículo...</p>
            </div>
        `;

        // ¡AQUÍ ES DONDE LA URL DEBE SER CORREGIDA PARA APUNTAR A TU RUTA MVC!
        // Asumiendo que tendrás una ruta /articles/{id} en tu backend
        fetch(`<span class="math-inline">\{BASE\_URL\}/articles/</span>{encodeURIComponent(articleId)}`) // Ejemplo de URL MVC
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                articleBody.innerHTML = html;
                if (typeof callback === 'function') {
                    setTimeout(callback, 100);
                }
                console.log(`Artículo ${articleId} cargado correctamente`);
            })
            .catch(error => {
                articleBody.innerHTML = `
                    <div class="alert alert-danger my-3">
                        <i class="fas fa-exclamation-circle"></i> 
                        Error al cargar el artículo: ${error.message}
                    </div>
                `;
                console.error("Error al cargar el artículo:", error);
            });
    }

    // Función para hacer scroll a un subtema
    function scrollToSubtopic(targetId) {
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            targetElement.classList.add("highlight-subtopic");
            targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
            setTimeout(() => {
                targetElement.classList.remove("highlight-subtopic");
            }, 2000);
            console.log(`Scroll realizado a subtema: ${targetId}`);
        } else {
            console.error(`No se encontró el elemento con ID: ${targetId}`);
        }
    }

    if (localStorage.getItem('asideVisible') === 'false') {
        document.querySelector('.toggle-aside-button').click();
    }
});