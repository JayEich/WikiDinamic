export const WikiService = {
    async guardarWiki(formData, url) { // 'url' como parámetro
      try {
        const response = await fetch(url, {
            method: "POST",
            body: formData,
          });

          const text = await response.text();
          console.log("📡 Respuesta cruda del servidor (guardarWiki):", text);

          if (!response.ok) {
            const errorData = JSON.parse(text);
            throw new Error(errorData.message || "Error en la petición al servidor");
          }

          return JSON.parse(text);

      } catch (error) {
        console.error("Error en WikiService.guardarWiki:", error);
        throw error; 
      }
    },

    async actualizarWiki(formData, url) { // ¡También actualiza esta para aceptar 'url'!
      try {
        const response = await fetch(url, { // Usa la 'url' pasada como parámetro
          method: "POST",
          body: formData,
        });

        const text = await response.text();
        console.log("📡 Respuesta cruda del servidor (actualizarWiki):", text);

        if (!response.ok) {
            const errorData = JSON.parse(text);
            throw new Error(errorData.message || "Error en la petición al servidor");
        }

        return JSON.parse(text);
      } catch (error) {
        console.error("Error en WikiService.actualizarWiki:", error);
        throw error;
      }
    }

  };