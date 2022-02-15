[
  {
    "Rates": {
      "ChargesFreight": { // Cargos en flete
        "DDF": { // Nombre del cargo 
          "40hc": [ // nombre  del contenedor al que  aplica ddf 
            {
              "surcharge_terms": "DDF", // Termino de venta que se remplaza solo si existe , sino toma el mismo nombre del surcharfe
              "surcharge_name": "DDF", // Nombre del cargo 
              "monto": "900.00", // Monto Total para DDF contenedor 40hc  , si existe otro cargo igual el monto aparece sumado aca 
              "currency": "EUR", // Moneda original si es un solo cargo o varios con la misma moneda  o la configurada por el cliente  si existe mas de un mismo cargo con diferente moneda 
              "calculation_name": "Per 40 HC", // Nombre del calculation type
              "type": "40hc", // Tipo de contenedor 
              "montoOrig": "0", // Campo que se usa para la suma de los montos  cuando hay mas de un cargo con diferentes monedas  
              "currency_company": "USD", // Moneda configurada por el cliente
              "currency_id": 46, // currency de la moneda original 
              "currency_company_id": 149, // id del currency configurada por el cliente 
              "markup": "0.00", // Monto total   del price level  si aplica 
              "markupConvert": 0, // Monto del price leve convertido a la moneda original
              "typemarkup": 0, // Tipo de markup si es por porcentaje o por monto total 
              "montoMarkup": "900.00", // Monto mas markup
              "montoMarkupO": "0" // Monto original del markup 
            }
          ],
          "99": {
            "1": {
              "surcharge_terms": "DDF",
              "surcharge_name": "DDF",
              "monto": "0.00",
              "markup": "0.00",
              "montoMarkup": "0.00",
              "currency": "USD",
              "calculation_name": "Per Container",
              "type": "99",
              "calculation_id": "5",
              "montoOrig": "0",
              "currency_company": "USD",
              "currency_id": 149,
              "currency_company_id": 149,
              "montoMarkupO": "0",
              "markupConvert": 0
            }
          } // Los arreglos 99 solo son usados por la aplicacion para unir y ordenar todas las monedas y cargos que apliquen para un contenedor especifico , su uso solo es valido para obtener el resultado final , este arreglo puede ser ignorado completamente . 
        }
      },
      "ChargesDestination": {
        "DDF": {// Nombre del cargo 
          "40": [// nombre  del contenedor al que  aplica ddf 
            {
              "surcharge_terms": "DDF",// Termino de venta que se remplaza solo si existe , sino toma el mismo nombre del surcharfe
              "surcharge_name": "DDF",// Nombre del cargo 
              "monto": "500.00", // Monto Total para DDF contenedor 40hc  , si existe otro cargo igual el monto aparece sumado aca 
              "currency": "USD", // Moneda original si es un solo cargo o varios con la misma moneda  o la configurada por el cliente  si existe mas de un mismo cargo con diferente moneda
              "calculation_name": "Per 40 \"",// Nombre del calculation type
              "type": "40",// Tipo de contenedor 
              "montoOrig": "0",// Campo que se usa para la suma de los montos  cuando hay mas de un cargo con diferentes monedas  
              "currency_company": "USD",// Moneda configurada por el cliente
              "currency_id": 149,// currency de la moneda original 
              "currency_company_id": 149,// id del currency configurada por el cliente 
              "markup": "0.00",// Monto total   del price level  si aplica 
              "markupConvert": 0,// Monto del price leve convertido a la moneda original
              "typemarkup": 0, // Tipo de markup si es por porcentaje o por monto total 
              "montoMarkup": "500.00",
              "montoMarkupO": "0"
            }
          ],
          "99": {
            "1": {
              "surcharge_terms": "DDF",
              "surcharge_name": "DDF",
              "monto": "0.00",
              "markup": "0.00",
              "montoMarkup": "0.00",
              "currency": "USD",
              "calculation_name": "Per Container",
              "type": "99",
              "calculation_id": "5",
              "montoOrig": "0",
              "currency_company": "USD",
              "currency_id": 149,
              "currency_company_id": 149,
              "montoMarkupO": "0",
              "markupConvert": 0
            }
          }// Los arreglos 99 solo son usados por la aplicacion para unir y ordenar todas las monedas y cargos que apliquen para un contenedor especifico , su uso solo es valido para obtener el resultado final , este arreglo puede ser ignorado completamente .
        }
      },
      "ChargesOrigin": {
        "CFD": {// Nombre del cargo 
          "20": [// nombre  del contenedor al que  aplica CFD 
            {
              "surcharge_terms": "Forfait",// Termino de venta que se remplaza solo si existe , sino toma el mismo nombre del surcharfe
              "surcharge_name": "CFD",// Nombre del cargo 
              "monto": "500.00",// Monto Total para CFD contenedor 40hc  , si existe otro cargo igual el monto aparece sumado aca 
              "currency": "USD", // Moneda original si es un solo cargo o varios con la misma moneda  o la configurada por el cliente  si existe mas de un mismo cargo con diferente moneda
              "calculation_name": "Per 20 \"",// Nombre del calculation type
              "type": "20",// Tipo de contenedor 
              "calculation_id": 2,//  Id del calculation type (OPCIONAL)
              "montoOrig": "0",// Campo que se usa para la suma de los montos  cuando hay mas de un cargo con diferentes monedas  
              "currency_company": "USD",// Moneda configurada por el cliente
              "currency_id": 149,// currency de la moneda original 
              "currency_company_id": 149,// id del currency configurada por el cliente 
              "markup": "0.00",// Monto total   del price level  si aplica 
              "markupConvert": 0,// Monto del price leve convertido a la moneda original
              "typemarkup": 0, // Tipo de markup si es por porcentaje o por monto total 
              "montoMarkup": "500.00",
              "montoMarkupO": "0"
            }
          ],
          "99": {
            "1": {
              "surcharge_terms": "Forfait",
              "surcharge_name": "CFD",
              "monto": "0.00",
              "markup": "0.00",
              "montoMarkup": "0.00",
              "montoMarkupO": "0",
              "currency": "USD",
              "calculation_name": "Per Container",
              "type": "99",
              "calculation_id": "5",
              "montoOrig": "0",
              "currency_company": "USD",
              "currency_id": 149,
              "currency_company_id": 149,
              "markupConvert": 0
            }
          }// Los arreglos 99 solo son usados por la aplicacion para unir y ordenar todas las monedas y cargos que apliquen para un contenedor especifico , su uso solo es valido para obtener el resultado final , este arreglo puede ser ignorado completamente .
        }
      },
      "ocean_freight": { // Valores de los fletes (Rutas )
        "type": "Ocean Freight", // Nombre o tipo , este dato para las rutas es constante  
        "detail": "Per Container", // tipo de calculation y como aplican los montos 
        "currency": "USD", // Moneda general para todos los contenedores 
        "20C": { // Tipo de contenedor 
          "price20": "100", // Precio base del contenedor 
          "currency20": "USD", // Moneda local (La misma general ) del contenedor 
          "markup20": "0", // Monto del markup para rutas si aplican 
          "markupConvert20": 0, // Monto de conversion a la moneda del cliente 
          "typemarkup20": "USD", // Moneda original del markup 
          "monto20": "100.00", // Monto final = Base + markupConvert20
          "montoMarkup20": "0" // campo opcional de los markup para calculos internos 
        },
        "40C": {  // Aplica la misma informacion descrita  en el contenedor 20C
          "price40": "200",
          "currency40": "USD",
          "markup40": "0",
          "markupConvert40": 0,
          "typemarkup40": "USD",
          "monto40": "200.00",
          "montoMarkup40": "0"
        },
        "40HC": { // Aplica la misma informacion descrita  en el contenedor 20C
          "price40hc": "300",
          "currency40hc": "USD",
          "markup40HC": "0",
          "markupConvert40HC": 0,
          "typemarkup40HC": "USD",
          "monto40HC": "300.00",
          "montoMarkup40HC": "0"
        },
        "40NOR": { // Aplica la misma informacion descrita  en el contenedor 20C
          "price40nor": "400",
          "currency40nor": "USD",
          "markup40NOR": "0",
          "markupConvert40NOR": 0,
          "typemarkup40NOR": "USD",
          "monto40NOR": "400.00",
          "montoMarkup40NOR": "0"
        },
        "45C": { // Aplica la misma informacion descrita  en el contenedor 20C 
          "price45": "500",
          "currency45": "USD",
          "idCurrency45": 149, // Este campo puede ser ignorado pues todos los contenedores aplican por un id general
          "markup45": "0",
          "markupConvert45": 0,
          "typemarkup45": "USD",
          "monto45": "500.00",
          "montoMarkup45": "0"
        }
      },
      "origin_port": { // Descripcion del puerto Origen
        "name": "Puerto Cabello",
        "code": "VEPBL"
      },
      "destination_port": {// Descripcion del puerto Destino
        "name": "La Guaira",
        "code": "VELAG"
      },
      "transit_time": 0,
      "via": null,
      "schedule": null,
      "carrier": "ONE",
      "currency": "USD",
      "total20": "600.00",
      "total40": "700.00",
      "total40HC": "800.00",
      "total40NOR": "400.00",
      "total45": "500.00"
    }
  }
]