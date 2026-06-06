# Quiniela App

La aplicación completa corre en Docker: Apache/PHP, dependencias Composer,
assets Vite, worker de colas y MariaDB.

## Levantar la aplicación

```bash
docker compose up --build -d
```

La aplicación queda disponible en <http://localhost:8081>.

En el primer arranque se ejecutan las migraciones y se cargan los datos
iniciales. Usuario administrador inicial:

- Usuario: `admin`
- Contraseña: `password`

Para ver el estado y los logs:

```bash
docker compose ps
docker compose logs -f app queue
```

Para detenerla:

```bash
docker compose down
```

Los datos de MariaDB y `storage` persisten en volúmenes Docker. Para eliminar
también esos datos y reiniciar desde cero:

```bash
docker compose down -v
```

Las variables pueden sobreescribirse creando un archivo `.env` a partir de
`.env.example`. No se necesita instalar PHP, Composer, Node.js ni MariaDB en el
equipo local.
