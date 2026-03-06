# 🔐 Guía de Configuración SSL - Proyecto Drawers

Esta guía detalla cómo se configuró el certificado SSL personalizado para el entorno de desarrollo, permitiendo el uso de dominios locales y puertos específicos.

---

## 1. 📋 Especificaciones del Certificado
El certificado actual es **auto-firmado** y ha sido generado con soporte para **Subject Alternative Name (SAN)**, lo cual es un requisito estricto de Google Chrome.

- **Dominios Soportados (Wildcard):** `drawers.docker`, `*.drawers.docker`, `localhost`.
- **IPs Soportadas:** `127.0.0.1`, `172.21.5.5`.
- **Puerto de Acceso:** `8443` (mapeado al `443` del contenedor).
- **Ubicación de archivos:** `apache_data/ssl/` (`apache.crt` y `apache.key`).

---

## 2. ⚙️ Configuración del Sistema (Host)

### A. Mapeo de Dominio Local
Para que el dominio `drawers.docker` funcione en tu máquina, debés editar el archivo de hosts:

```bash
# Ejecutar en la terminal de Linux
sudo bash -c 'echo "127.0.0.1 drawers.docker" >> /etc/hosts'
```

### B. Confianza en Google Chrome (Linux)
Para eliminar el aviso de "No es seguro", debés inyectar el certificado en la base de datos NSS de Chrome:

1. **Eliminar versiones anteriores (si existen):**
   ```bash
   certutil -d sql:$HOME/.pki/nssdb -D -n "Drawers-SSL"
   ```

2. **Importar el nuevo certificado:**
   ```bash
   certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n "Drawers-Wildcard" -i /home/juan/VirtualMachines/Docker/Drawers/apache_data/ssl/apache.crt
   ```

---

## 3. 🚀 Acceso al Sitio
Una vez configurado el host y el certificado, podés acceder mediante:

- **URL Principal:** [https://drawers.docker:8443](https://drawers.docker:8443)
- **Acceso Local:** [https://localhost:8443](https://localhost:8443)

---

## 🛠️ Notas Técnicas de Regeneración
Si necesitás regenerar el certificado manualmente en el futuro, se utilizó el siguiente archivo de configuración OpenSSL (`apache_data/ssl/openssl.conf`):

```ini
[req]
distinguished_name = req_distinguished_name
x509_extensions = v3_req
prompt = no

[req_distinguished_name]
CN = drawers.docker

[v3_req]
keyUsage = critical, digitalSignature, keyEncipherment
extendedKeyUsage = serverAuth
subjectAltName = @alt_names

[alt_names]
DNS.1 = drawers.docker
DNS.2 = *.drawers.docker
DNS.3 = localhost
IP.1 = 127.0.0.1
IP.2 = 172.21.5.5
```

Y el comando de generación:
```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout apache.key -out apache.crt -config openssl.conf -extensions v3_req
```

---
*Última actualización: Marzo 2026*
