# Internet Speed Booster - Full Stack Web Application

## Overview

This is a comprehensive internet speed testing and optimization web application built with React frontend and Express backend. The application provides users with tools to test their internet speed, get optimization recommendations, and access educational content about improving network performance. It's designed as a Progressive Web App (PWA) with SEO optimization and monetization through Google AdSense integration.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript
- **Build Tool**: Vite for fast development and optimized production builds
- **UI Library**: shadcn/ui components built on Radix UI primitives
- **Styling**: Tailwind CSS with custom design system
- **State Management**: TanStack Query for server state management
- **Routing**: Wouter for lightweight client-side routing
- **Form Handling**: React Hook Form with Zod validation

### Backend Architecture
- **Runtime**: Node.js with Express.js framework
- **Language**: TypeScript with ES modules
- **API Design**: RESTful endpoints for feedback and blog content
- **Email Service**: Nodemailer for feedback notifications
- **Session Management**: PostgreSQL session store

### Database Layer
- **ORM**: Drizzle ORM for type-safe database operations
- **Database**: PostgreSQL (configured for Neon serverless)
- **Schema**: Structured tables for feedback and blog posts
- **Migrations**: Drizzle Kit for database schema management

## Key Components

### Core Features
1. **Speed Test Tool**: WebRTC-based internet speed testing with download, upload, and ping measurements
2. **Optimization Tips**: Cache clearing, DNS recommendations, and performance suggestions
3. **Device Analysis**: Browser and OS detection with personalized recommendations
4. **Feedback System**: User feedback collection with email notifications
5. **Blog System**: SEO-optimized blog for educational content

### UI Components
- **Modular Design**: Reusable components following atomic design principles
- **Accessibility**: ARIA-compliant components with keyboard navigation
- **Responsive Design**: Mobile-first approach with adaptive layouts
- **Dark Mode**: Theme switching capability built into design system

### SEO & Performance
- **Meta Tags**: Comprehensive Open Graph and Twitter Card meta tags
- **Structured Data**: JSON-LD schema for FAQ and organization data
- **PWA Features**: Service worker, manifest, and offline capabilities
- **Performance**: Lazy loading, code splitting, and optimized assets

## Data Flow

### Speed Test Flow
1. User initiates speed test from main interface
2. Client performs network measurements using fetch API and WebRTC
3. Results displayed with sharing capabilities
4. Optional feedback collection for improvement

### Content Management Flow
1. Blog posts stored in PostgreSQL with slug-based routing
2. SEO-friendly URLs with proper meta tags
3. Static content served through Express with Vite integration
4. Search functionality for content discovery

### Feedback Processing
1. User submits feedback through validated form
2. Data stored in PostgreSQL with timestamp
3. Email notification sent to admin via Nodemailer
4. Success confirmation displayed to user

## External Dependencies

### Core Dependencies
- **Database**: Neon PostgreSQL serverless database
- **Email Service**: SMTP provider for Nodemailer integration
- **CDN**: Font delivery through Google Fonts
- **Analytics**: Google AdSense integration for monetization

### Development Tools
- **Type Safety**: TypeScript across full stack
- **Code Quality**: ESLint and Prettier configuration
- **Build Tools**: Vite with React plugins and optimization
- **Development**: Hot reload and error overlays for debugging

### UI Libraries
- **Component Library**: Radix UI primitives with custom styling
- **Icons**: Lucide React icon library
- **Styling**: Tailwind CSS with custom design tokens
- **Forms**: React Hook Form with Zod schema validation

## Deployment Strategy

### Build Process
1. **Frontend**: Vite builds optimized React application to `dist/public`
2. **Backend**: esbuild compiles TypeScript server to `dist/index.js`
3. **Database**: Drizzle migrations applied during deployment
4. **Assets**: Static files served through Express in production

### Environment Configuration
- **Development**: Hot reload with Vite dev server proxy
- **Production**: Single Express server serving both API and static files
- **Database**: Environment-based connection strings
- **Email**: SMTP configuration through environment variables

### Performance Optimization
- **Code Splitting**: Automatic route-based code splitting
- **Asset Optimization**: Image compression and lazy loading
- **Caching**: Browser caching headers and service worker
- **CDN Integration**: Static asset delivery optimization

### Monitoring & Analytics
- **Error Tracking**: Console logging and error boundaries
- **Performance**: Web Vitals monitoring capabilities
- **User Analytics**: Google AdSense integration for insights
- **Feedback Loop**: Direct user feedback collection system