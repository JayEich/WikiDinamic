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
        // Crear el botón de toggle si no existe
        if (!document.querySelector(".toggle-aside-button")) {
            const toggleButton = document.createElement("button");
            toggleButton.className = "toggle-aside-button menu-visible";
            toggleButton.innerHTML = "☰";
            toggleButton.setAttribute("title", "Mostrar/Ocultar menú");
            document.body.appendChild(toggleButton);
            
            // Añadir el evento de click al botón
            toggleButton.addEventListener("click", function() {
                const isHidden = articleMenu.classList.toggle("hidden-aside");
                
                // Actualizar el icono del botón
                this.innerHTML = isHidden ? "☰" : "✖";
                
                // Actualizar la posición del botón
                this.classList.toggle("menu-visible", !isHidden);
                
                // Expandir o contraer el contenido principal
                articleContent.classList.toggle("content-expanded", isHidden);
                
                // Log para depuración
                console.log(`Aside ${isHidden ? 'oculto' : 'visible'}, contenido ${isHidden ? 'expandido' : 'normal'}`);
            });
        }
    }
    
    // Función para configurar los eventos de artículos y subtemas
    function setupArticlesAndSubtopics() {
        // 1. Manejo de clicks en artículos
        const articleLinks = document.querySelectorAll(".article-link");
        articleLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                
                // Resaltar el artículo activo
                articleLinks.forEach(el => el.classList.remove("active"));
                this.classList.add("active");
                
                // Cargar el contenido
                const articleId = this.dataset.id;
                loadArticle(articleId);
            });
        });
        
        // 2. Manejo de toggle de subtemas
        const toggleButtons = document.querySelectorAll(".toggle-subtopics");
        toggleButtons.forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevenir la propagación del evento
                
                // Obtener el ID del contenedor de subtemas
                const targetId = this.dataset.target;
                const subtopicsContainer = document.getElementById(targetId);
                
                // Toggle de la clase expanded
                const isExpanded = subtopicsContainer.classList.toggle("expanded");
                
                // Actualizar el icono
                this.classList.toggle("expanded", isExpanded);
                
                // Log para depuración
                console.log(`Toggle subtopics for ${targetId}: ${isExpanded ? 'expanded' : 'collapsed'}`);
            });
        });
        
        // 3. Manejo de click en subtemas
        const subtopicLinks = document.querySelectorAll(".subtopic-link");
        subtopicLinks.forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                
                // Asegurarse de que el artículo está cargado
                const articleId = this.dataset.article;
                const targetId = this.dataset.target;
                
                // Resaltar el artículo padre si es necesario
                const parentArticleLink = document.querySelector(`.article-link[data-id="${articleId}"]`);
                if (parentArticleLink && !parentArticleLink.classList.contains("active")) {
                    articleLinks.forEach(el => el.classList.remove("active"));
                    parentArticleLink.classList.add("active");
                    
                    // Cargar el artículo primero y luego hacer scroll
                    loadArticle(articleId, function() {
                        scrollToSubtopic(targetId);
                    });
                } else {
                    // Si el artículo ya está cargado, solo hacer scroll
                    scrollToSubtopic(targetId);
                }
                
                // Marcar el subtema como activo
                subtopicLinks.forEach(el => el.classList.remove("active"));
                this.classList.add("active");
            });
        });
    }
    
    // Función para cargar un artículo
    function loadArticle(articleId, callback) {
        // Mostrar loading
        articleBody.innerHTML = `
            <div class="text-center my-5">
                <div class="loading-spinner"></div>
                <p class="mt-3">Cargando artículo...</p>
            </div>
        `;
        
        // Hacer la petición AJAX
        fetch(`article.php?id=${encodeURIComponent(articleId)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                // Insertar el contenido
                articleBody.innerHTML = html;
                
                // Ejecutar el callback si existe
                if (typeof callback === 'function') {
                    setTimeout(callback, 100); // Pequeño retraso para asegurar que el DOM se ha actualizado
                }
                
                // Log para depuración
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
            // Añadir un efecto visual temporal para destacar el elemento
            targetElement.classList.add("highlight-subtopic");
            
            // Hacer scroll
            targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
            
            // Eliminar el efecto después de un tiempo
            setTimeout(() => {
                targetElement.classList.remove("highlight-subtopic");
            }, 2000);
            
            console.log(`Scroll realizado a subtema: ${targetId}`);
        } else {
            console.error(`No se encontró el elemento con ID: ${targetId}`);
        }
    }
    
    // Comprobar al cargar si el menú debe estar visible u oculto (por defecto visible)
    // Esto es útil para mantener el estado en recargas de página
    if (localStorage.getItem('asideVisible') === 'false') {
        document.querySelector('.toggle-aside-button').click();
    }
});