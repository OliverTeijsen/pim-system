<h1>🚀 Product Manager</h1>
<h2>📚 Overview</h2>

<h4> 🚀 &nbsp;Some Tools I Have Used and Learned</h4>
<p align="left">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/phpstorm/phpstorm-original.svg" alt="vscode" width="45" height="45"/>
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" alt="bash" width="45" height="45"/>
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="php" width="45" height="45"/>
</p>

This Symfony web app allows users to manage products. Key features:

📝 Product Management: Create, edit, and delete products with details like name, description, type, quantity, and image.
🔒 Authentication: Basic auth system to restrict access to authorized users.
🖼️ Product Display: Card-based grid layout with product images.

<strong>🕰️ Development Timeline</strong>

Morning (9:30 AM): Set up Git, Docker, and database.
Noon (12:00 PM): Implemented basic product form.
Afternoon (3:00 PM): Added auth, enhanced product form, updated display.

<strong>🛠️ Installation</strong>

1) Clone repo: git clone https://github.com/OliverTeijsen/pim-system.git

2) Navigate: cd product-manager

3) Start dev env: docker-compose up -d

4) Install deps: docker-compose exec php composer install

5) Create DB, migrate:

6) docker-compose exec php bin/console doctrine:database:create

7) docker-compose exec php bin/console doctrine:migrations:migrate


8) Start server: docker-compose exec php bin/console server:start

App at http://localhost:8080.


<strong>🚀 Usage</strong>

Auth: Users can register/login to access product management.
Products: Logged-in users can create/edit/delete products.
Display: Products shown in card grid with optional images.

<strong>🚀 API Usage</strong>
This API will response in JSON format

<p>GET method /api/products (Retrieve all products)</p>
<p>GET method /api/products/{id} (Retrieve product by id)</p>
<p>POST method /api/products (Create products)</p>
<p>PUT method /api/products/{id} (Update product by id)</p>
<p>DEL method /api/products/{id} (Delete product by id)</p>

<strong>🤝 Contributing</strong>

Fork repo.
Create feature/fix branch.
Make changes, test.
Commit, push to fork.
Submit pull request.
