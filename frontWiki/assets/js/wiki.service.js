// Para el crud de las wikis con tiny
export const WikiService = {
    async guardarWiki(formData) {
      try {
        const response = await fetch("wikicrud/wiki.crear.php", {
            method: "POST",
            body: formData,
          });
          
          const text = await response.text();
          console.log("📡 Respuesta cruda del servidor:", text);
          
          if (!response.ok) {
            throw new Error("Error en la petición al servidor");
          }
          
          return JSON.parse(text);
          
      } catch (error) {
        console.error("Error al guardar la wiki:", error);
        throw error; 
      }
    },

    async actualizarWiki(formData) {
      try {
        const response = await fetch("wikicrud/wiki.editar.php", {
          method: "POST",
          body: formData,
        });
    
        const text = await response.text();
        console.log("📡 Respuesta cruda del servidor:", text);
    
        if (!response.ok) {
          throw new Error("Error en la petición al servidor");
        }
    
        return JSON.parse(text);
      } catch (error) {
        console.error("Error al editar la wiki:", error);
        throw error;
      }
    }
    

  };
  