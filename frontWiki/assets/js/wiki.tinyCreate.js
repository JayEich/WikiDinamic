import { WikiService } from './wiki.service.js';

window.Wikiconcept = {
    articleCounter: 0,
    subarticleCounters: {},

    init: function () {
        this.setupPrevisualizacion();
        this.setupColorPickers();
        this.setupArticuloHandlers();
        this.setupSubmitHandler();
        this.applyPreviewColors();
    },

    // ---------------- PREVISUALIZACION ----------------
    setupPrevisualizacion: function () {
        const titleInput = document.getElementById("title");
        const descriptionInput = document.getElementById("description");
        const imageCardInput = document.getElementById("wikiImageCard");
        const imageLogoInput = document.getElementById("wikiImageLogo");

        titleInput.addEventListener("input", function () {
            document.getElementById("previewTitle").textContent = this.value || "Título de la Wiki";
        });

        descriptionInput.addEventListener("input", function () {
            document.getElementById("previewDescription").textContent = this.value || "Descripción de la Wiki...";
        });

        imageCardInput.addEventListener("change", function (event) {
            Wikiconcept.previewImage(event, "previewImageCard", "../img/placeholder-card.jpg");
        });

        imageLogoInput.addEventListener("change", function (event) {
            Wikiconcept.previewImage(event, "previewImageLogo", "../assets/img/placeholder-logo.jpg");
        });
    },

    previewImage: function (event, targetId, fallbackPath) {
        const file = event.target.files[0];
        const target = document.getElementById(targetId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                target.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            target.src = fallbackPath;
        }
    },

    applyPreviewColors: function () {
        const primary = document.getElementById("primaryColor").value;
        const secondary = document.getElementById("secondaryColor").value;
        const tertiary = document.getElementById("tertiaryColor").value;

        const nav = document.getElementById("previewNav");
        nav.style.backgroundColor = primary;
        nav.style.color = secondary;
        nav.querySelectorAll("*").forEach(el => el.style.color = secondary);

        const card = document.getElementById("previewCard");
        card.style.backgroundColor = secondary;
        card.style.color = tertiary;
        card.style.border = `2px solid ${tertiary}`;
        document.getElementById("previewTitle").style.color = tertiary;
    },

    // ---------------- COLOR PICKERS ----------------
    setupColorPickers: function () {
        ["primary", "secondary", "tertiary"].forEach(color => {
            const colorInput = document.getElementById(`${color}Color`);
            const codeInput = document.getElementById(`${color}ColorCode`);

            colorInput.addEventListener("input", function () {
                codeInput.value = this.value;
                Wikiconcept.applyPreviewColors();
            });

            codeInput.addEventListener("input", function () {
                colorInput.value = this.value;
                Wikiconcept.applyPreviewColors();
            });
        });
    },

    // ---------------- ARTICULOS ----------------
    setupArticuloHandlers: function () {
        document.getElementById("addArticle").addEventListener("click", function () {
            Wikiconcept.articleCounter++;
            Wikiconcept.subarticleCounters[Wikiconcept.articleCounter] = 0;

            const container = document.getElementById("articlesContainer");
            const articleId = Wikiconcept.articleCounter;

            const articleDiv = document.createElement("div");
            articleDiv.className = "article-block mt-4 p-3 border rounded";
            articleDiv.style.backgroundColor = "#f8f9fa";
            articleDiv.innerHTML = `
                <h4>Artículo ${articleId}</h4>
                <div class="mb-3">
                    <label class="form-label">Título del Artículo:</label>
                    <input type="text" name="article_title[]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contenido Primario:</label>
                    <textarea class="article-content form-control" name="article_content[]"></textarea>
                </div>
                <div class="subarticlesContainer mt-3" id="subarticles-${articleId}"></div>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-secondary addSubarticle" data-article="${articleId}">+ Agregar Subtema</button>
                    <button type="button" class="btn btn-sm btn-danger d-none removeSubarticle" data-article="${articleId}">- Quitar Último Subtema</button>
                </div>
            `;

            container.appendChild(articleDiv);
            Wikiconcept.updateArticleButtons();

            tinymce.init({
                selector: `#articlesContainer .article-block:last-child .article-content`,
                license_key: 'gpl',
                height: 300,
                language: 'es',
                content_css: '../assets/css/styles.tiny.css',
                skin: 'oxide',
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                branding: false,
                setup: function (editor) {
                    editor.on('init', function () {
                        editor.getContainer().style.borderRadius = '8px';
                        editor.getContainer().style.boxShadow = '0px 4px 10px rgba(0, 0, 0, 0.1)';
                    });
                }
            });
        });

        document.getElementById("articlesContainer").addEventListener("click", function (event) {
            const articleId = event.target.getAttribute("data-article");

            if (event.target.classList.contains("addSubarticle")) {
                Wikiconcept.subarticleCounters[articleId]++;
                const subId = Wikiconcept.subarticleCounters[articleId];
                const container = document.getElementById(`subarticles-${articleId}`);

                const subDiv = document.createElement("div");
                subDiv.className = "subarticle-block mt-3 p-2 border rounded";
                subDiv.style.backgroundColor = "#e9ecef";
                subDiv.innerHTML = `
                    <h5>Subartículo ${subId}</h5>
                    <div class="mb-2">
                        <label class="form-label">Título del Subtema (Subtitulo):</label>
                        <input type="text" name="subarticle_title[${articleId}][]" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contenido del Subartículo:</label>
                        <textarea class="subarticle-content form-control" name="subarticle_content[${articleId}][]"></textarea>
                    </div>
                `;

                container.appendChild(subDiv);
                document.querySelector(`.removeSubarticle[data-article="${articleId}"]`).classList.remove("d-none");

                tinymce.init({
                    selector: `#subarticles-${articleId} .subarticle-block:last-child .subarticle-content`,
                    license_key: 'gpl',
                    height: 300,
                    language: 'es',
                    content_css: '../assets/css/styles.tiny.css',
                    skin: 'oxide',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    branding: false,
                    setup: function (editor) {
                        editor.on('init', function () {
                            editor.getContainer().style.borderRadius = '8px';
                            editor.getContainer().style.boxShadow = '0px 4px 10px rgba(0, 0, 0, 0.1)';
                        });
                    }
                });
            }

            if (event.target.classList.contains("removeSubarticle")) {
                const container = document.getElementById(`subarticles-${articleId}`);
                if (container.lastChild) {
                    container.lastChild.remove();
                    Wikiconcept.subarticleCounters[articleId]--;
                    if (Wikiconcept.subarticleCounters[articleId] === 0) {
                        event.target.classList.add("d-none");
                    }
                }
            }
        });

        document.getElementById("removeArticle").addEventListener("click", function () {
            if (Wikiconcept.articleCounter > 0) {
                document.getElementById("articlesContainer").lastChild.remove();
                Wikiconcept.articleCounter--;
                delete Wikiconcept.subarticleCounters[Wikiconcept.articleCounter];
            }
            Wikiconcept.updateArticleButtons();
        });
    },

    updateArticleButtons: function () {
        const removeButton = document.getElementById("removeArticle");
        removeButton.classList.toggle("d-none", Wikiconcept.articleCounter === 0);
    },

    // ---------------- ENVIO FORMULARIO ----------------
    setupSubmitHandler: function () {
        const btn = document.getElementById("btnGuardarWiki");

        if (!btn) {
            console.warn("No se encontró el botón con id 'btnCrearWiki'. ¿Estás seguro de que el formulario está cargado?");
            return;
        }
        
    
        btn.addEventListener("click", async function () {
            try {
                tinymce.triggerSave();
    
                const form = document.getElementById("wikiForm");
                const formData = new FormData(form);
    
                // Log para revisar los datos antes de enviarlos
                console.log("Datos del formulario antes de enviarlos:");
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
    
                // Agregar artículos manualmente
                document.querySelectorAll("input[name='article_title[]']").forEach((input, i) => {
                    formData.append(`article_title[${i}]`, input.value);
                });
                document.querySelectorAll("textarea[name='article_content[]']").forEach((textarea, i) => {
                    formData.append(`article_content[${i}]`, textarea.value);
                });
    
                // Subartículos por artículo
                Object.keys(Wikiconcept.subarticleCounters).forEach(articleIndex => {
                    const titles = document.querySelectorAll(`input[name='subarticle_title[${articleIndex}][]']`);
                    const contents = document.querySelectorAll(`textarea[name='subarticle_content[${articleIndex}][]']`);
    
                    titles.forEach((input, subIndex) => {
                        formData.append(`subarticle_title[${articleIndex}][${subIndex}]`, input.value);
                        console.log(`subarticle_title[${articleIndex}][${subIndex}]: ${input.value}`); // Log adicional para subartículos
                    });
                    contents.forEach((textarea, subIndex) => {
                        formData.append(`subarticle_content[${articleIndex}][${subIndex}]`, textarea.value);
                        console.log(`subarticle_content[${articleIndex}][${subIndex}]: ${textarea.value}`); // Log adicional para subartículos
                    });
                });
    
                // Enviar al servidor usando el servicio
                const data = await WikiService.guardarWiki(formData);
    
                const msg = document.getElementById("message");
                msg.innerHTML = data.success
                    ? `<div class="alert alert-success">${data.message}</div>`
                    : `<div class="alert alert-danger">${data.message}</div>`;
    
            } catch (error) {
                console.error(error);
                document.getElementById("message").innerHTML = `<div class="alert alert-danger">Error al guardar la wiki.</div>`;
            }
        });
    }
    
};

// Inicia todo al cargar
window.addEventListener("DOMContentLoaded", () => Wikiconcept.init());