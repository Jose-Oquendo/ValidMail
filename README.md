# ValMail API

ValMail es una API diseñada para validar y manejar correos electrónicos, con funciones que permiten verificar su formato, dominio y la presencia de ciertas palabras clave en el nombre del correo. Esta API utiliza la librería `Egulias/EmailValidator` para asegurar que los correos sean válidos según las reglas estándar de RFC y DNS.

## Instalación

Para ejecutar esta API, primero debes instalar las dependencias necesarias utilizando Composer:

```bash
composer install
```

Asegúrate de tener las siguientes dependencias instaladas en tu proyecto:

- slim/slim - Framework utilizado para gestionar las rutas y el ciclo de vida de las solicitudes.
- egulias/email-validator - Paquete utilizado para validar la estructura de los correos electrónicos.
- vlucas/phpdotenv - Para cargar las variables de entorno desde un archivo .env.

## Endpoints de validacion de formato

Para la validacion de correos el contenido body de las solicitudes debe ser similar a:
```json
{
    "email": "correo@ejemplo.com"
}
```

1. `GET/`

Este endpoint devuelve un mensaje de bienvenida a la API.

Respuesta:
```json
{
    "message": "Bienvenido a ValMail! Haga uso de nuestros endpoints para la validación y manejo de correos electrónicos :D"
}
```

2. `POST /eval/egulias`
Este endpoint valida un correo electrónico utilizando las validaciones RFC y DNS, asegurándose de que sea un correo electrónico válido según los estándares.

3. `POST /eval/format`
Este endpoint valida solo el formato de un correo electrónico, asegurándose de que siga la estructura estándar nombre@dominio.com.

4. `POST /eval/domain`
Este endpoint valida que el dominio del correo tenga registros DNS válidos (registro MX).

## Endpoints para comprobacion de correo

Para la validacion de la existencia de un correo electronico, se implementa el consumo de la API de `Hunter.io`, cuya documentacion puedes consultar en: https://hunter.io/api-documentation/v2.
Para el uso de esta API externa, debes crear tu usuario en la plataforma, teniendo en cuenta que el plan gratuito cuenta con 50 validaciones por mes.

1. `POST /catch/email/`
Este endpoint retorna la evaluacion hecha por la API si el correo existe en un servidor SMTP y con su respectiva validacion de formato.

## Uso

- Realiza una solicitud POST a los endpoints con el correo electrónico en el cuerpo de la solicitud.
- La API devolverá un mensaje con el estado de la validación del correo electrónico.
- Asegúrate de tener un archivo .env configurado para tu entorno. Debes llevar en tu archivo .env las siguientes variables:
```.env
HUNTER_KEY=
HUNTER_API=
```