
import { WikiService } from './wiki.service.js';

window.Wikiconcept = {
    articleCounter: document.querySelectorAll('.article-block').length,
    subarticleCounters: {},
    deletedArticles: [],
    deletedSubarticles: [],
    


    init: function () {
        this.setupPrevisualizacion();
        this.setupColorPickers();
        this.setupArticuloHandlers();
        this.setupSubmitHandler();
        this.applyPreviewColors();
        this.initTinyMCE();
        this.initSubarticleCounters();
        this.appendDeleteButtonsToLoadedArticles();
        
        this.articleCounter = document.querySelectorAll('.article-block').length;
    },
    
    initSubarticleCounters: function () {
        document.querySelectorAll('.article-block').forEach((container, index) => {
            const subarticlesContainer = container.querySelector('.subarticlesContainer');
            if (subarticlesContainer) {
                this.subarticleCounters[index] = subarticlesContainer.querySelectorAll('.subarticle-block').length;
            } else {
                this.subarticleCounters[index] = 0;
            }
        });
    },

    //PREVISUALIZACION
    setupPrevisualizacion: function () {
        const titleInput = document.getElementById("title");
        const descriptionInput = document.getElementById("description");
        const imageCardInput = document.getElementById("wikiImageCard");
        const imageLogoInput = document.getElementById("wikiImageLogo");

        titleInput.addEventListener("input", function () {
            document.getElementById("previewTitle").textContent = this.value || "Título de la Wiki";
            document.getElementById("previewNavTitle").textContent = this.value || "Título del nav";
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

    //Manejo de ARTICULOS/SUB
    appendDeleteButtonsToLoadedArticles: function () {
        document.querySelectorAll('.article-block').forEach((block, index) => {
            const actions = block.querySelector('.mt-2');
            const addBtn = actions?.querySelector('.addSubarticle');
            const articleId = addBtn?.getAttribute('data-article') || (index + 1);

            const header = block.querySelector('h4');
            if (header && !block.querySelector('.deleteArticle')) {
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'btn btn-sm btn-outline-danger deleteArticle';
                deleteBtn.setAttribute('data-article', index + 1);
                deleteBtn.innerText = 'Eliminar Articulo';

                const wrap = document.createElement('div');
                wrap.className = 'd-flex justify-content-between align-items-center';
                wrap.appendChild(header);
                wrap.appendChild(deleteBtn);

                block.prepend(wrap);
            }
        });
    },

    setupArticuloHandlers: function () {
        document.getElementById("addArticle").addEventListener("click", () => this.addArticle());

        // Cuando el usuario agrega un nuevo subartículo:
        document.getElementById("articlesContainer").addEventListener("click", function (event) {
            if (event.target.classList.contains("addSubarticle")) {
                const articleBlock = event.target.closest('.article-block');
                const loopIndex = Array.from(document.querySelectorAll('.article-block')).indexOf(articleBlock);
                
                if (!Wikiconcept.subarticleCounters[loopIndex]) {
                    Wikiconcept.subarticleCounters[loopIndex] = 0;
                }
                Wikiconcept.subarticleCounters[loopIndex]++;
                
                const subId = Wikiconcept.subarticleCounters[loopIndex];
                const container = document.getElementById(`subarticles-${loopIndex}`) 
                    || articleBlock.querySelector('.subarticlesContainer');
                
                const subDiv = document.createElement("div");
                subDiv.className = "subarticle-block mt-3 p-2 border rounded new-subarticle";
                subDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Subtema ${subId}</h5>
                        <button type="button" class="btn btn-sm btn-outline-danger quitarSubarticulo">Quitar</button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Título del Subtema:</label>
                        <input type="text" name="subarticle_title[${loopIndex}][]" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contenido del Subartículo:</label>
                        <textarea class="subarticle-content form-control" name="subarticle_content[${loopIndex}][]"></textarea>
                    </div>
                `;
                
                if (container) {
                    container.appendChild(subDiv);
                    // Inicializa TinyMCE en el nuevo subartículo
                    setTimeout(() => {
                        Wikiconcept.initTinyMCE(`.subarticle-block:last-child .subarticle-content`);
                    }, 100);
                }
            }

            
            // Quitar artículo nuevo
            if (event.target.classList.contains("removeArticle")) {
                event.target.closest(".new-article").remove();
                Wikiconcept.articleCounter--;
            }

            // Quitar subartículo nuevo
            if (event.target.classList.contains("quitarSubarticulo")) {
                const articleId = event.target.closest('.subarticle-block')
                                    .closest('.subarticlesContainer')
                                    .id.split('-')[1];
                event.target.closest('.subarticle-block').remove();
                Wikiconcept.subarticleCounters[articleId]--;
            }

            // Eliminación de artículo existente
            if (event.target.classList.contains("deleteArticle")) {
                const articleBlock = event.target.closest(".article-block");
                const uuidInput = articleBlock.querySelector("input[name='article_uuid[]']");
                Swal.fire({
                    title: '¿Eliminar este artículo?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (uuidInput) {
                            Wikiconcept.deletedArticles.push(uuidInput.value);
                        }
                        articleBlock.remove();
                    }
                });
            }

            // Eliminación de subartículo existente
            if (event.target.classList.contains("deleteSubarticle")) {
                const subarticleBlock = event.target.closest('.subarticle-block');
                const uuid = event.target.getAttribute("data-sub-id");
                Swal.fire({
                    title: '¿Eliminar este subartículo?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (uuid) {
                            Wikiconcept.deletedSubarticles.push(uuid);
                        }
                        subarticleBlock.remove();
                    }
                });
            }

        });
    },

    addArticle: function () {
        Wikiconcept.articleCounter++;
        const articleIndex = Wikiconcept.articleCounter - 1; // Para mantener base-0 en índices
        Wikiconcept.subarticleCounters[articleIndex] = 0;
    
        const container = document.getElementById("articlesContainer");
    
        const articleDiv = document.createElement("div");
        articleDiv.className = "article-block mt-4 p-3 border rounded new-article";
        articleDiv.setAttribute('data-article-id', articleIndex);
        articleDiv.style.backgroundColor = "#f8f9fa";
        articleDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <h4>Artículo ${Wikiconcept.articleCounter}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger removeArticle" data-article="${articleIndex}">Quitar</button>
                </div>
            </div>
            <input type="hidden" name="article_uuid[]" value="">
            <div class="mb-3">
                <label class="form-label">Título del Artículo:</label>
                <input type="text" name="article_title[]" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contenido Primario:</label>
                <textarea class="article-content form-control" name="article_content[]"></textarea>
            </div>
            <div class="subarticlesContainer mt-3" id="subarticles-${articleIndex}"></div>
            <div class="mt-2 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-secondary addSubarticle" data-article="${articleIndex}">+ Agregar Subtema</button>
            </div>
        `;
    
        container.appendChild(articleDiv);
        // Inicializa TinyMCE después de agregar el elemento al DOM
        setTimeout(() => {
            Wikiconcept.initTinyMCE(`#articlesContainer .article-block:last-child .article-content`);
        }, 100);
    },

    initTinyMCE: function (selector = '.article-content, .subarticle-content') {
        // Destruir instancias existentes si el selector es específico
        if (selector !== '.article-content, .subarticle-content') {
            const element = document.querySelector(selector);
            if (element) {
                const id = element.id || `tinymce-${Math.random().toString(36).substring(2, 9)}`;
                if (!element.id) element.id = id;
                
                if (tinymce.get(id)) {
                    tinymce.remove(`#${id}`);
                }
            }
        }
        
        tinymce.init({
            selector,
            license_key: 'gpl',
            height: 300,
            language: 'es',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            menubar: true,
            branding: false,
            paste_as_text: true,
            setup: function(editor) {
                editor.on('change', function() {
                    tinymce.triggerSave();
                });
            }
        });
    },

    setupSubmitHandler: function () {
        const btns = document.querySelectorAll("#btnGuardarWiki");
        
        btns.forEach(btn => btn.addEventListener("click", async function () {
            try {
                // Primero guardamos el contenido de TinyMCE en los textareas
                tinymce.triggerSave();
                
                // ✋ VALIDACIÓN DESPUÉS DE GUARDAR TINYMCE
                const errors = [];
                
                // Validar título y descripción de la wiki
                const title = document.getElementById("title").value.trim();
                const description = document.getElementById("description").value.trim();
                
                if (!title) errors.push("El título de la wiki no puede estar vacío.");
                if (!description) errors.push("La descripción de la wiki no puede estar vacía.");
                
                // Validar cada artículo
                document.querySelectorAll("input[name='article_title[]']").forEach((input, i) => {
                    if (!input.value.trim()) errors.push(`El título del artículo ${i + 1} está vacío.`);
                });
                
                // Validar contenido de artículos con TinyMCE
                document.querySelectorAll(".article-content").forEach((textarea, i) => {
                    if (!textarea.value.trim()) errors.push(`El contenido del artículo ${i + 1} está vacío.`);
                });
                
                // Mostrar errores si hay
                if (errors.length > 0) {
                    Swal.fire({
                        title: "Hay errores en el formulario",
                        html: errors.map(e => `<p>${e}</p>`).join(""),
                        icon: "error"
                    });
                    return;
                }
                
                // Continuar si todo está bien
                const form = document.getElementById("wikiEdit");
                const formData = new FormData(form);
                
                // Adjuntar los eliminados
                Wikiconcept.deletedArticles.forEach(uuid => {
                    formData.append("deleted_articles[]", uuid);
                });
                Wikiconcept.deletedSubarticles.forEach(uuid => {
                    formData.append("deleted_subarticles[]", uuid);
                });
                
                const data = await WikiService.actualizarWiki(formData);
                const msg = document.getElementById("message");
                
                msg.innerHTML = data.success
                    ? `<div class="alert alert-success">${data.message}</div>`
                    : `<div class="alert alert-danger">${data.message}</div>`;
                
            } catch (error) {
                console.error(error);
                document.getElementById("message").innerHTML =
                    `<div class="alert alert-danger">Error al guardar la wiki: ${error.message}</div>`;
            }
        }));
    }
    
};

window.addEventListener("DOMContentLoaded", () => Wikiconcept.init());