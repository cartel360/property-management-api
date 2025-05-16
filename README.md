# **Project Setup Guide**

This guide will walk you through setting up the project, configuring your environment, running migrations, and getting the app up and running with Docker support.

## **1. Prerequisites**

Before setting up the application, ensure you have the following installed:

* **PHP** 8.2 or higher
* **Composer** (for managing PHP dependencies)
* **Node.js** (for managing frontend assets)
* **Docker** (for containerization, optional)

---

## **2. Clone the Repository**

Start by cloning the repository to your local machine:

```bash
git clone https://github.com/cartel360/property-management-api.git
cd property-management-api
```

---

## **3. Set Up Environment Variables**

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

In the `.env` file, configure your SQLite database:

```env
DB_CONNECTION=sqlite
```

---

## **4. Install PHP Dependencies**

Run the following command to install all the necessary PHP dependencies, including Laravel Telescope (which is already defined in the `composer.json` file):

```bash
composer install
```

Since **Laravel Telescope** is already listed in the `composer.json` file, it will be installed automatically when you run `composer install`. There’s no need to install it separately.

---

## **5. Run Migrations & Seeders**

Laravel will automatically create the SQLite database file when running migrations, as long as the `.env` file is configured correctly. No need to manually create the SQLite database file.

Run the following command to migrate the database and seed it with initial data:

```bash
php artisan migrate --seed
```

---

## **6. Set Up Laravel Telescope**

If you are using **Laravel Telescope**, it’s already included in the `composer.json` file, so there is no need for a separate installation.

Run the following commands to publish the Telescope configuration and run the necessary migrations:

```bash
php artisan telescope:install
php artisan migrate
```

After this, you can access **Laravel Telescope** at `/telescope` in your browser.

---

## **7. Run Tests (Optional)**

If you have integrated tests, you can run them with PHPUnit to ensure everything is working correctly:

```bash
php artisan test
```

This command will run the tests defined in the `tests/` directory.

---

## **8. Run Telescope Profiling Script**

You have a script that makes requests to the application’s endpoints and generates a performance report. To execute this script:

```bash
php artisan profile:endpoints --user=1 --iterations=3
```

This command will run the profiling script with the specified user ID and number of iterations. The script will make requests to the endpoints and generate a report based on the performance data collected.

This will run the profiling script and generate a performance improvement report based on the data collected.

The generated report will be saved in the storage\app\private\reports directory. You can view the report by opening the file in your browser or text editor.


## **9. Set Up Docker (Optional)**

The project already includes a `Dockerfile` for containerization. To run the app with Docker Compose, follow these steps:

### 9.1 **Install Docker Compose**

Make sure you have Docker and Docker Compose installed on your machine. You can follow the official installation guides:

* [Install Docker](https://docs.docker.com/get-docker/)
* [Install Docker Compose](https://docs.docker.com/compose/install/)

### 9.2 **Build and Start the Application with Docker Compose**

1. **Build and Start Containers**
   In your project directory, run the following command to build and start the containers defined in the `docker-compose.yml` file:

   ```bash
   docker-compose up --build
   ```

   This command will:

   * Build the Docker images based on the `Dockerfile` and `docker-compose.yml`.
   * Start the app and Nginx containers.

2. **Access the Application**
   Once the containers are up and running, you can access your Laravel app at:

   ```url
   http://localhost:8000
   ```

   This will serve your app via Nginx, which is set up to forward requests to the PHP-FPM service.

### 9.3 **Run Containers in Detached Mode (Optional)**

If you'd prefer to run the containers in the background (detached mode), you can add the `-d` flag:

```bash
docker-compose up --build -d
```

This will run the containers in the background, allowing you to continue using your terminal for other tasks. To view the logs for each container, you can use:

```bash
docker-compose logs app    # For the PHP-FPM container
docker-compose logs webserver  # For the Nginx container
```

### 9.4 **Stop the Containers**

To stop and remove the containers when you're done, use the following command:

```bash
docker-compose down
```

This will stop the running containers and remove them, but leave the images intact.

---


## **10. Accessing the Application**

Once everything is set up and running, open your browser and go to:

```
http://localhost:8000
```

This will bring up the Laravel application.

---

## **10. Accessing the Swagger API Documentation**
To access the Swagger API documentation, navigate to:

```
http://localhost:8000/api/documentation
```

This will display the API documentation generated by Swagger, allowing you to explore the available endpoints and their details.


## **12. Summary of Steps**

1. Clone the repository.
2. Configure environment variables in `.env`.
3. Install PHP dependencies with `composer install`.
4. Run migrations and seed the database with `php artisan migrate --seed`.
5. (Optional) Set up Laravel Telescope with `php artisan telescope:install`.
6. (Optional) Run tests with `php artisan test`.
7. (Optional) Run the Telescope profiling script with `php artisan profile:endpoints --user=1 --iterations=3`.
8. (Optional) Build and run the Docker container using the `docker build` and `docker run` commands.
9. Access the application at `http://localhost:8000`.

---

## **Troubleshooting**

* **Docker**: If Docker containers are not starting, make sure Docker is running and that you’ve correctly built the containers.
* **Telescope**: If Telescope is not showing up at `/telescope`, make sure you’ve run `php artisan telescope:install` and migrated the Telescope tables with `php artisan migrate`.

---

## API Documentation
### Swagger API Documentation

The API documentation is available via Swagger UI. You can access it by visiting the following URL:

[Swagger API Documentation](http://localhost:8000/api/documentation)

Please ensure your server is running on localhost:8000 (or replace with your actual URL).

Alternatively, you can view the Postman collection documentation at:

[Postman API Documentation](https://documenter.getpostman.com/view/9038976/2sB2qWH4FF)

---

## Scalability Architecture

Scaling the API horizontally involves designing an architecture that can handle high traffic loads, maintain low-latency responses, and efficiently utilize resources across multiple services and instances. The goal is to ensure that the system can manage growing user demand and traffic volume without compromising performance or reliability.

### 1. **Web Tier Scaling**

* **Stateless Architecture**: The API is built on a stateless architecture using **JWT authentication** for user sessions, ensuring that each request can be handled by any server without requiring session storage. This is essential for scaling horizontally since no state needs to be shared between instances.
* **Load Balancing**: A load balancer (e.g., **Nginx**, **AWS ALB/ELB**, or **Kubernetes ingress controllers**) will distribute incoming API requests across multiple server instances. Load balancing ensures even distribution of traffic, preventing any single server from becoming a bottleneck.
* **Auto-Scaling**: Auto-scaling allows the web tier to dynamically scale based on demand. By monitoring metrics such as CPU usage and request rate, new instances can be spun up automatically to handle spikes in traffic. When traffic drops, unnecessary instances are terminated to save on costs.

### 2. **Database Scaling**

Horizontal scaling of the database is crucial to maintain high performance as the system grows:

* **Read Replicas**: To offload read-heavy operations, we can implement a **read-replica** strategy with one primary (write) database and multiple replicas. This allows the API to handle a higher volume of read requests by distributing them across multiple database nodes.
* **Sharding**: As the database grows, **sharding** (partitioning the data across different databases based on certain criteria, e.g., region or user ID) helps keep individual databases smaller and improves query performance. Each shard can handle a subset of the traffic, reducing the load on any single database.
* **Connection Pooling**: Connection pooling using tools like **PgBouncer** helps optimize the use of database connections by reusing idle connections, which reduces overhead and improves database performance, especially when the number of concurrent requests grows.

### 3. **Queue Workers for Asynchronous Jobs**

For non-blocking tasks such as sending notifications, processing payments, or generating reports, scaling background workers is essential:

* **Dynamic Worker Scaling**: Use auto-scaling features (e.g., in Kubernetes or via cloud providers) to dynamically scale the number of workers based on the workload. This ensures that the system can handle spikes in background job processing without affecting the performance of the main API.
* **Queue Prioritization**: To handle different job types efficiently, background jobs can be prioritized. For example, high-priority jobs like payment processing can be placed on a dedicated queue to ensure they are processed before lower-priority tasks.

### 4. **Caching Strategy**

Caching plays a critical role in improving API performance by reducing database load and speeding up response times for frequently requested data:

* **Application Caching**: Use an in-memory cache (e.g., **Redis**) to store commonly accessed data such as active properties, user profiles, or frequently queried lists. By caching the results of expensive database queries, the API can serve responses faster without hitting the database on every request.
* **Database Caching**: For complex or slow queries, **materialized views** or **precomputed results** can be used to store query results that don’t change frequently. This can significantly reduce the time taken to fetch data from the database.

### 5. **Service Decomposition Roadmap**

As the application grows, breaking the monolithic API into smaller, more manageable microservices can help scale individual parts of the system:

* **Phase 1**: Move specific functionality (such as payment processing) to a dedicated **payment microservice**. This allows the payment system to scale independently of the main API.
* **Phase 2**: Introduce **Elasticsearch** for more efficient and scalable search capabilities. This is particularly useful if the API deals with large amounts of data and complex search queries.
* **Phase 3**: Extract file management (e.g., document storage) into a separate service or use a cloud-native solution like AWS S3 to store and serve files.

### 6. **Monitoring & Scaling Triggers**

Monitoring system health and performance metrics is essential for proactive scaling:

* **CPU Utilization**: If CPU usage exceeds a set threshold (e.g., 70%), more server instances should be added to handle the increased load.
* **API Response Time**: If API response times increase beyond acceptable limits, additional instances or workers can be provisioned to handle the traffic more effectively.
* **Queue Backlog**: If the number of pending background jobs exceeds a threshold, additional workers can be added to process these jobs quickly.
* **Database Replication Lag**: Monitor the lag between the primary and replica databases. If the lag exceeds a certain threshold, it may indicate that the system is under heavy load and needs to scale.

### 7. **Recommended Infrastructure**

To efficiently scale the API, the infrastructure should leverage cloud-native services and containerization:

* Use **Kubernetes** or **AWS ECS** to manage and scale containerized applications.
* Implement **CI/CD** pipelines to automate deployments, ensuring that new versions of the API can be deployed quickly and consistently across all instances.
* Utilize **AWS RDS** for managed database scaling, leveraging read replicas and automated backups to ensure high availability and durability of data.

---

By implementing these strategies, the API can scale horizontally, ensuring high performance and availability as demand increases. Horizontal scaling ensures that the system can distribute load across multiple servers, improve the performance of critical database operations, and handle growing traffic volumes without compromising reliability or user experience.

---

## My Architectural Thought Process:

When designing the app, I focused on ensuring the structure was clean, scalable, and easy to maintain over time. Here’s a breakdown of the key decisions I made:

### Modular Structure:

I decided to structure the application around key business domains like `Auth/`, `Leases/`, `Payments/`, etc., all within the same `app/` folder. Rather than creating completely separate `app/` structures for each domain (which would be the case in a fully Domain-Driven Design approach), I opted for a simpler modular structure. Each domain has its own dedicated subdirectory within `app/`, making it easier to maintain and scale while avoiding unnecessary complexity. This approach also keeps related files together, improving code organization and maintainability.

### Separation of Concerns:

To ensure a clean, maintainable codebase, I made sure to clearly separate different responsibilities within the application. For instance:

* **Controllers** handle incoming HTTP requests and contain the logic to respond to them.
* **Repositories** handle data management and interactions with the database.
* **Requests** manage validation logic for incoming data.
  By keeping these concerns separated, I ensure that the code remains clean and easy to test. It also makes it easier for developers to work on specific aspects of the application without interfering with other parts.

### Service-Oriented Approach:

I used **service providers** and **repositories** to promote a **service-oriented architecture**. This ensures that business logic, data access, and external service integrations (like notifications) are all encapsulated in separate, manageable components. For example, payment processing is handled by a dedicated payment service, and notifications are managed by their own service class. This structure allows for more flexibility and makes it easier to extend or swap out services in the future.

### Scalability & Maintenance:

I made sure to design the app with scalability in mind:

* **Repositories** help separate data logic from business logic, which makes it easier to scale or modify the data layer independently of the application’s core functionality.
* The **queue system** is used for handling background tasks like processing payments or sending notifications. This allows the app to handle tasks asynchronously, ensuring high availability and responsiveness without overloading the system.

### Extensibility:

As the app grows, I want it to be easy to extend without introducing unnecessary complexity. By organizing the application in this way, I can add new features or modify existing functionality with minimal friction. If I need to add a new service or feature, I can do so without disrupting the overall structure.

### Clear Responsibility:

Each directory and file in the project has a **clear responsibility**. Whether it's handling HTTP requests, interacting with the database, or processing jobs, every part of the application follows a well-defined role. I followed Laravel’s conventions for organizing the app because they are intuitive and widely used, which makes it easier for new developers to contribute to the project.

---

### Why This Architecture Works for Me:

The structure is designed to be **clean**, **maintainable**, and **modular**, in line with Laravel's best practices. By grouping everything within a single `app/` folder but organizing it by domains, I ensure flexibility and scalability without overcomplicating things. This approach makes it easy to extend and maintain the app, and it allows for straightforward addition of new features as the application grows.
