// Ajusta la ruta de importación para reflejar la nueva ubicación de wiki.service.js
// Por ejemplo, si movemos wiki.service.js a frontWiki/src/wiki/services/wiki.js
import { WikiService } from '../services/wiki.js'; // Ruta relativa desde create/index.js a services/wiki.js

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
            document.getElementById("previewNavTitle").textContent = this.value || "Título del nav"; // Añadí esto, si lo necesitas
        });

        descriptionInput.addEventListener("input", function () {
            document.getElementById("previewDescription").textContent = this.value || "Descripción de la Wiki...";
        });

        imageCardInput.addEventListener("change", function (event) {
            // Rutas de imágenes relativas a public/assets, Vite se encarga de eso.
            Wikiconcept.previewImage(event, "previewImageCard", "/wikiconceptMVC/public/assets/img/cardGeneric.jpg");
        });

        imageLogoInput.addEventListener("change", function (event) {
            // Rutas de imágenes relativas a public/assets
            Wikiconcept.previewImage(event, "previewImageLogo", "/wikiconceptMVC/public/assets/img/placeholder-logo.jpg");
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
        document.getElementById("addArticle").addEventListener("click", () => this.addArticle());

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
                subDiv.style.backgroundColor = "#e9ecef"; // Color de fondo para subartículos
                subDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Subartículo <span class="math-inline">\{subId\}</h5\>