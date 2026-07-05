# Coolify Setup Guide

## Quick Start

### Server Info
- **VPS:** 217.15.167.23
- **Coolify UI:** http://217.15.167.23:8000
- **Traefik Proxy:** http://217.15.167.23 (ports 80, 443, 8080)

### Docker Containers
```bash
# List containers
docker ps

# View logs
docker logs biotree-app
docker logs biotree-mysql
docker logs biotree-redis
```

### Network
- All containers on `coolify` network
- Internal DNS: `<container-name>.coolify`

### Useful Commands

```bash
# Restart app
docker restart biotree-app

# View logs
docker logs biotree-app -f

# SSH into container
docker exec -it biotree-app sh

# Check MySQL
docker exec biotree-mysql mysql -u root -p'biotree_root_pass_2024' biotree

# Check Redis
docker exec biotree-redis redis-cli -a 'biotree_redis_pass_2024'
```

### Env Variables in Container
```bash
docker exec biotree-app cat .env
```

### Traefik Config
Dynamic configs at: `/data/coolify/proxy/dynamic/`
