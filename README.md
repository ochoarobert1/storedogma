# Docker Dogma Store

WordPress e-commerce site running on Docker with WooCommerce and Elementor.

## Theme Configuration

- **Active Theme**: `official-dogma` (child theme)
- **Parent Theme**: `hello-elementor`

## Plugins

### E-commerce
- WooCommerce

### Page Builder
- Elementor

### Admin & Development
- Advanced Custom Fields
- Admin Columns Pro

## Docker Setup

### Prerequisites
- Docker
- Docker Compose

### Environment Variables
Create a `.env` file with:
```env
WORDPRESS_DB_HOST=db:3306
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=your_password
WORDPRESS_DB_NAME=wordpress
WORDPRESS_TABLE_PREFIX=wp_
MYSQL_ROOT_PASSWORD=root_password
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=your_password
```

### Running the Application

```bash
docker-compose up -d
```

### Access Points
- **WordPress**: http://localhost:1666
- **Adminer**: http://localhost:1667
- **Database**: localhost:3306

### Stopping the Application
```bash
docker-compose down
```

## File Structure
- `.source/themes/` - WordPress themes
- `.source/plugins/` - WordPress plugins  
- `.source/uploads/` - Media uploads
- `.docker/mysql/data/` - Database files
- `.docker/mysql/backup/` - Database backups