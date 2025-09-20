# About API Documentation

This document describes the About API endpoints for frontend consumption, following the same pattern as Homepage API.

## Base URL
```
/api/v1/about
```

## Endpoints

### 1. Get All About Data
**GET** `/api/v1/about?lang={language}`

Returns structured about page data for specified language (defaults to Indonesian).

**Query Parameters:**
- `lang` - Language code (`id` for Indonesian, `en` for English). Default: `id`

**Response Example:**
```json
{
  "success": true,
  "message": "About page data retrieved successfully",
  "data": {
    "our_story": {
      "title": "Our Story",
      "content": "<p>Our story content...</p>",
      "image": "/img/story.png"
    },
    "vision": {
      "title": "Vision",
      "content": "<p>Our vision...</p>"
    },
    "mission": {
      "title": "Mission",
      "items": [
        {
          "id": 1,
          "text": "First mission",
          "order": 1
        }
      ]
    },
    "logo_philosophy": {
      "title": "Logo Philosophy",
      "image": "/img/logo.png",
      "points": [
        {
          "id": 1,
          "text": "First philosophy point",
          "order": 1
        }
      ]
    },
    "closing_statement": "<p>Closing statement...</p>"
  },
  "meta": {
    "language": "en",
    "bilingual_enabled": true,
    "generated_at": "2024-01-15T10:30:00.000Z"
  },
  "response_time_ms": 45.67
}
```

### 2. Get Specific Section
**GET** `/api/v1/about/{section}?lang={language}`

Returns data for a specific about section in specified language.

**Path Parameters:**
- `section` - Section name (`our-story`, `vision`, `mission`, `logo-philosophy`, `closing-statement`)

**Query Parameters:**
- `lang` - Language code (`id` for Indonesian, `en` for English). Default: `id`

**Response Example:**
```json
{
  "success": true,
  "message": "About section 'our-story' retrieved successfully",
  "data": {
    "section": "our-story",
    "our_story": {
      "title": "Our Story",
      "content": "<p>Our story content...</p>",
      "image": "/img/story.png"
    },
    "meta": {
      "language": "en",
      "bilingual_enabled": true,
      "generated_at": "2024-01-15T10:30:00.000Z"
    }
  },
  "response_time_ms": 23.45
}
```

## Alternative Configuration API

### Get About Configurations
**GET** `/api/v1/configurations/about?lang={language}`

Returns raw about configurations (same as main about endpoint but under configurations namespace).

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Invalid section. Valid sections: our-story, vision, mission, logo-philosophy, closing-statement",
  "data": {
    "available_sections": ["our-story", "vision", "mission", "logo-philosophy", "closing-statement"]
  }
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Section data not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Failed to fetch about data: [error details]"
}
```

## Usage Examples

### Frontend JavaScript/Vue.js
```javascript
// Get all about data in Indonesian (default)
const aboutData = await fetch('/api/v1/about').then(res => res.json());

// Get all about data in English
const aboutDataEn = await fetch('/api/v1/about?lang=en').then(res => res.json());

// Get specific section in Indonesian
const ourStory = await fetch('/api/v1/about/our-story').then(res => res.json());

// Get specific section in English
const visionEn = await fetch('/api/v1/about/vision?lang=en').then(res => res.json());
```

### cURL Examples
```bash
# Get all about data (Indonesian default)
curl -X GET "https://yourdomain.com/api/v1/about"

# Get all about data in English
curl -X GET "https://yourdomain.com/api/v1/about?lang=en"

# Get vision section in English
curl -X GET "https://yourdomain.com/api/v1/about/vision?lang=en"

# Get mission section in Indonesian
curl -X GET "https://yourdomain.com/api/v1/about/mission?lang=id"
```

## Notes

1. All endpoints are public (no authentication required)
2. Language parameter accepts `id` (Indonesian) or `en` (English)
3. Content fields may contain HTML from rich text editor
4. Mission items and philosophy points are sorted by `order` field
5. Images paths are relative and should be prefixed with your domain/storage URL