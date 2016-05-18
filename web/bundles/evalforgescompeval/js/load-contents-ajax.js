/**
 * Se crea el objeto net.CargadorContenidos para realizar peticiones GET al servidor
 */

//Variable global que encapsula todas las propiedades y métodos relativos a las
// operaciones relacionadas con las comunicaciones por red
var net = new Object();

// Constantes empleadas por el objeto XMLHttpRequest
net.READY_STATE_UNINITIALIZED=0;
net.READY_STATE_LOADING=1;
net.READY_STATE_LOADED=2;
net.READY_STATE_INTERACTIVE=3;
net.READY_STATE_COMPLETE=4;

// Constructor del objeto CargadorContenidos
net.CargadorContenidos = function(url, funcion, funcionError) {
    // Se inicializan algunas variables
	this.url = url;
  	this.req = null;
  	this.onload = funcion;
  	// Si no se ha definido, se emplea una función de error genérica definida más adelante
  	this.onerror = (funcionError) ? funcionError : this.defaultError;
  	// Método responsable de cargar el recurso solicitado
  	this.cargaContenidoXML(url);
}

// Los métodos empleados por el objeto net.cargaContenidos se definen mediante su prototipo
net.CargadorContenidos.prototype = {

	// Envia la petición HTTP y llama a la función que procesa la respuesta
	cargaContenidoXML: function(url) {
		// Se obtiene una instancia del objeto XMLHttpRequest en función del tipo de navegador
    	if(window.XMLHttpRequest) {
      		this.req = new XMLHttpRequest();
    	}
    	else if(window.ActiveXObject) {
      		this.req = new ActiveXObject("Microsoft.XMLHTTP");
    	}

    	if(this.req) {
      		try {
          		// Se almacena la instancia del objeto net.cargadorContenidos
        		var loader = this;
        		// Función encargada de procesar la respuesta del servidor
        		this.req.onreadystatechange = function() {
          			loader.onReadyState();
        		}
        		this.req.open('GET', url, true);
        		this.req.send(null);
      		} catch(err) {
        		this.onerror.call(this);
      		}
    	}
		},

		// Función encargada de gestionar la respuesta del servidor
  	onReadyState: function() {
    	var req = this.req;
    	var ready = req.readyState;
    	// Se comprueba que la respuesta del servidor está disponible y es correcta
        if(ready == net.READY_STATE_COMPLETE) {
        	var httpStatus = req.status;
        	if(httpStatus == 200 || httpStatus == 0) {
        		// Función que realmente procesa la respuesta del servidor:
        		// ejecuta la función externa con el objeto net.CargadorContenidos
        		// accesible en el interior de la función mediante el objeto this
            	this.onload.call(this);
          	}
          	else {
            	this.onerror.call(this);
          	}
        }
  	},

  	// Muestra un mensaje del error producido y el valor de propiedades de la petición HTTP
  	defaultError: function() {
    	alert("Se ha producido un error al obtener los datos"
      		+ "\n\nreadyState:" + this.req.readyState
      		+ "\nstatus: " + this.req.status
      		+ "\nheaders: " + this.req.getAllResponseHeaders());
  	}
}

