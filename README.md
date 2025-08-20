# Techni-Elec Website

## Installation
1. Clone the repository:
   ```bash
   git clone git@github.com:KelloTek/techni-elec-website.git
   ```

2. Navigate to the project directory:
   ```bash
   cd techni-elec-website
    ```

3. Create certificate for local development:
   ```bash
   make generate-certs
   ```

4. Initialize docker:
   ```bash
   make build
   make up
   ```

5. Install the dependencies:
   ```bash
   make composer-install
   ```

6. Install the dependencies:
   ```bash
   make yarn-install
   ```

7. Start the development server:
   ```bash
   make yarn-watch
   ```

8. Open your browser and go to `http://localhost` or `https://localhost` to view the website.
9. You can configure the project with `compose.yaml`, `.env` file and `docker-dev` folder.
10. You can use `make cmd-php` or `make cmd-node` to run commands in the php or node container respectively.
11. You can use `make down` to stop the containers.
