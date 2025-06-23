<p align="center">
  <img src="https://symfony.com/logos/symfony_black_03.png" alt="Symfony" width="200"/>
</p>

# Proyecto Symfony

Este proyecto utiliza el framework **Symfony 7.3**, un framework PHP moderno y robusto para el desarrollo de aplicaciones web.

## Características principales

- **Autenticación JWT:** Integración con LexikJWTAuthenticationBundle para autenticación segura basada en tokens JWT.
- **Validaciones:** Uso de Symfony Form Types para validar datos de entrada en los endpoints (por ejemplo, login y registro).
- **CRUD:** Operaciones CRUD completas utilizando Doctrine ORM y entidades PHP.
- **ORM Doctrine:** Mapeo objeto-relacional para interactuar con la base de datos de forma sencilla y eficiente.
- **Entity Class:** Definición de entidades PHP que representan las tablas de la base de datos, con anotaciones y validaciones.
- **Documentación:** Endpoints documentados con NelmioApiDocBundle y OpenAPI para facilitar el consumo de la API.

## Requisitos

- PHP >= 8.2
- Composer
- Extensiones PHP recomendadas: `pdo_mysql`, `openssl`, `sodium`, `mbstring`, `xml`
- MySQL o MariaDB
- Node.js y Yarn/NPM (opcional, para assets)

## Instalación

1. Clona el repositorio:
   ```bash
   git clone <URL-del-repositorio>
   cd seven
   ```

2. Instala las dependencias de PHP:
   ```bash
   composer install
   ```

3. Copia el archivo de entorno y configura tus variables:
   ```bash
   cp .env .env.local
   # Edita .env.local con tus credenciales de base de datos y claves JWT
   ```

4. (Opcional) Instala dependencias de frontend:
   ```bash
   yarn install
   # o
   npm install
   ```

## Versión

Este proyecto utiliza **Symfony 7.x**.

## Comandos útiles para la base de datos

### Crear la base de datos

```bash
php bin/console doctrine:database:create
```

### Crear las tablas (migraciones)

```bash
php bin/console doctrine:migrations:migrate
```

### Generar una nueva migración

```bash
php bin/console make:migration
```

### Actualizar el esquema de la base de datos

```bash
php bin/console doctrine:schema:update --force
```

---

Para más información,

