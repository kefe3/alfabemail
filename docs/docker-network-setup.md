# Docker Network Setup for Mailcow Integration

This guide explains how to set up and test the Docker network configuration for integrating the alfabemail Laravel application with mailcow-dockerized.

## Overview

The alfabemail application uses Docker Compose to create a shared network with mailcow-dockerized, allowing the Laravel application to communicate with the Mailcow API without exposing the API to the public internet.

## Docker Compose Configuration

The `docker-compose.yml` file is already configured with the necessary network setup:

```yaml
services:
  app:
    networks:
      - alfabemail
      - mailcow_shared

networks:
  alfabemail:
    driver: bridge
  mailcow_shared:
    external: true
    name: ${MAILCOW_DOCKER_NETWORK:-mailcowdockerized_mailcow-network}
```

## Environment Variables

Add these variables to your `.env` file:

```env
# Mailcow Configuration
MAILCOW_API_BASE_URL=https://mailcow/api/v1
MAILCOW_API_KEY=your-mailcow-api-key-here
MAILCOW_DOMAIN=alfabe.co
MAILCOW_DEFAULT_QUOTA_MB=2048
MAILCOW_DOCKER_NETWORK=mailcowdockerized_mailcow-network
```

## Setup Instructions

### 1. Start Mailcow-Dockerized

First, ensure mailcow-dockerized is running:

```bash
cd /path/to/mailcow-dockerized
docker-compose up -d
```

### 2. Verify Mailcow Network

Check that the mailcow network exists:

```bash
docker network ls | grep mailcow
```

You should see something like:
```
mailcowdockerized_mailcow-network
```

### 3. Start Alfabemail Application

Start the Laravel application:

```bash
docker-compose up -d
```

### 4. Test Network Connectivity

Test that the alfabemail container can reach the mailcow API:

```bash
# Enter the app container
docker exec -it alfabemail-app bash

# Test connectivity to mailcow API
curl -H "X-API-Key: your-api-key" https://mailcow/api/v1/get/domain/alfabe.co
```

### 5. Run Application Tests

Run the Mailcow connection tests:

```bash
docker exec -it alfabemail-app php artisan test tests/Feature/MailcowConnectionTest.php
```

## Troubleshooting

### Network Not Found Error

If you get an error like "network mailcowdockerized_mailcow-network not found":

1. Ensure mailcow-dockerized is running
2. Check the correct network name:
   ```bash
   docker network ls
   ```
3. Update the `MAILCOW_DOCKER_NETWORK` environment variable if needed

### API Connection Failed

If API calls fail:

1. Verify the API key is correct
2. Check that the API URL is accessible from within the container
3. Ensure mailcow is running and the API is enabled

### Permission Issues

If you get permission errors:

1. Check that the Laravel application has the correct permissions to access the API
2. Verify the API key has the necessary permissions

## Testing the Integration

### Manual Testing

1. Create a test student through the Filament admin panel
2. Verify that a mailbox is created in mailcow
3. Test QR code login functionality
4. Check that email functionality works

### Automated Testing

Run the full test suite:

```bash
docker exec -it alfabemail-app php artisan test
```

## Security Considerations

- The API key is stored in the `.env` file and should not be committed to version control
- The shared network allows internal communication without exposing the API to the internet
- Ensure proper firewall rules are in place on the mailcow server
- Regularly rotate API keys

## Performance Optimization

- Use Redis for caching API responses
- Implement proper error handling and retry logic
- Monitor API usage and implement rate limiting if needed
- Consider using a queue for bulk operations like student creation

## Monitoring

Monitor the following metrics:

- API response times
- Error rates
- Network connectivity issues
- Mailbox creation success rates

Use Laravel's logging system to track API calls and errors:

```php
Log::info('Mailcow API call', ['endpoint' => $endpoint, 'response_time' => $time]);
```
