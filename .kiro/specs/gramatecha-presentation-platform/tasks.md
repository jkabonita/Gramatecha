# Implementation Plan: GRAMATECHA Presentation Platform

## Overview

This implementation plan breaks down the GRAMATECHA presentation platform into discrete coding tasks. The platform will be built using Laravel 13 for the backend, Vue.js 3 with Inertia.js 3 for the frontend, and Cloudinary for file storage and delivery. The implementation follows a layered approach: database setup, backend services and controllers, frontend components, and finally integration and testing.

## Tasks

- [x] 1. Set up database schema and authentication
  - Create migration to add `role` column to users table with enum type ('admin', 'regular_user')
  - Create migration for presentations table with all required columns (title, description, filename, file_size, cloudinary_public_id, cloudinary_url, uploaded_by)
  - Add appropriate indexes for role, uploaded_by, created_at, and cloudinary_public_id columns
  - Create database seeder for creating an initial admin user for testing
  - Run migrations and verify schema
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2. Implement core models and relationships
  - [ ] 2.1 Update User model with role functionality
    - Add `role` to fillable fields
    - Add `isAdmin()` and `isRegularUser()` helper methods
    - Define `presentations()` relationship (hasMany)
    - _Requirements: 1.3_
  
  - [~] 2.2 Create Presentation model
    - Define fillable fields (title, description, filename, file_size, cloudinary_public_id, cloudinary_url, uploaded_by)
    - Add casts for file_size (integer) and timestamps
    - Define `uploader()` relationship (belongsTo User)
    - Add `getFormattedFileSize()` helper method for human-readable sizes
    - Add `getCloudinaryViewUrl()` helper method for generating optimized URLs
    - _Requirements: 2.7, 3.3_
  
  - [ ]* 2.3 Write property test for metadata persistence
    - **Property 4: Metadata Persistence**
    - **Validates: Requirements 2.7, 3.3**
  
  - [ ]* 2.4 Write unit tests for model relationships and helper methods
    - Test User-Presentation relationship
    - Test formatted file size output
    - Test Cloudinary URL generation
    - _Requirements: 2.7_

- [ ] 3. Configure Cloudinary integration
  - [~] 3.1 Install Cloudinary PHP SDK via Composer
    - Run `composer require cloudinary/cloudinary_php`
    - _Requirements: 2.4, 8.1_
  
  - [~] 3.2 Add Cloudinary configuration to services config
    - Add cloudinary config array to `config/services.php` with cloud_name, api_key, api_secret from env variables
    - Set default values for cloud_name (io7qeps0) and api_key (975981621953623)
    - _Requirements: 2.9, 8.6_
  
  - [~] 3.3 Create CloudinaryService class
    - Implement constructor that initializes Cloudinary client with credentials from config
    - Implement `uploadPresentation()` method that uploads files to Cloudinary with resource_type 'raw'
    - Implement `deletePresentation()` method that removes files from Cloudinary
    - Implement `getPresentationUrl()` method that generates delivery URLs with optimization parameters
    - Implement `generateUniquePublicId()` private method using UUID
    - _Requirements: 2.4, 6.3, 5.2, 5.7_
  
  - [ ]* 3.4 Write property test for unique identifier generation
    - **Property 5: Unique Identifier Generation**
    - **Validates: Requirements 2.8, 8.2**
  
  - [ ]* 3.5 Write unit tests for CloudinaryService with mocked Cloudinary API
    - Mock Cloudinary upload and verify correct parameters
    - Mock Cloudinary delete and verify correct public_id
    - Test URL generation with optimization parameters
    - _Requirements: 2.4, 6.3, 5.7_

- [ ] 4. Implement PresentationService with security features
  - [~] 4.1 Create PresentationService class
    - Inject CloudinaryService dependency
    - Implement `createPresentation()` method that orchestrates file upload, sanitization, malware scanning, and metadata storage
    - Implement `updatePresentationMetadata()` method for updating title and description
    - Implement `deletePresentation()` method that deletes from both Cloudinary and database
    - _Requirements: 2.2, 2.4, 3.5, 6.3, 6.4_
  
  - [~] 4.2 Implement filename sanitization
    - Implement `sanitizeFilename()` private method
    - Remove path traversal patterns (../, ..\, etc.)
    - Remove null bytes and special characters
    - Keep only alphanumeric, hyphens, underscores, dots
    - Prevent double extensions
    - _Requirements: 10.2_
  
  - [~] 4.3 Implement malware scanning integration
    - Implement `scanFileForMalware()` private method
    - Choose and integrate malware scanning solution (ClamAV, VirusTotal, or MetaDefender)
    - Handle scan failures by rejecting upload
    - Log malware detection events
    - _Requirements: 10.3, 10.4_
  
  - [ ]* 4.4 Write property test for filename sanitization
    - **Property 12: Filename Sanitization**
    - **Validates: Requirements 10.2**
  
  - [ ]* 4.5 Write property test for malware detection
    - **Property 13: Malware Detection**
    - **Validates: Requirements 10.3, 10.4**
  
  - [ ]* 4.6 Write unit tests for PresentationService business logic
    - Test createPresentation flow with mocked dependencies
    - Test updatePresentationMetadata
    - Test deletePresentation removes from both Cloudinary and database
    - _Requirements: 2.2, 3.5, 6.4_

- [ ] 5. Create form request validation classes
  - [~] 5.1 Create PresentationStoreRequest
    - Define validation rules: title (required, string, max:255), description (nullable, string, max:1000), file (required, file, mimes:ppt,pptx, max:51200)
    - Implement authorize() method to check if user can create presentations
    - Add custom error messages for validation failures
    - _Requirements: 2.2, 2.5, 3.1, 3.2_
  
  - [~] 5.2 Create PresentationUpdateRequest
    - Define validation rules: title (required, string, max:255), description (nullable, string, max:1000)
    - Implement authorize() method to check if user can update the presentation
    - _Requirements: 3.5_
  
  - [ ]* 5.3 Write property test for file type validation
    - **Property 2: File Type Validation**
    - **Validates: Requirements 2.2, 2.3**
  
  - [ ]* 5.4 Write property test for file size validation
    - **Property 3: File Size Validation**
    - **Validates: Requirements 2.5, 2.6**
  
  - [ ]* 5.5 Write property test for description length validation
    - **Property 6: Description Length Validation**
    - **Validates: Requirements 3.2**
  
  - [ ]* 5.6 Write unit tests for form request validation rules
    - Test valid inputs pass validation
    - Test invalid file types fail validation
    - Test oversized files fail validation
    - Test long descriptions fail validation
    - _Requirements: 2.2, 2.5, 3.2_

- [ ] 6. Implement authorization with policies
  - [~] 6.1 Create PresentationPolicy
    - Implement `viewAny()` - all authenticated users can view list
    - Implement `view()` - all authenticated users can view single presentation
    - Implement `create()` - only admins can create presentations
    - Implement `update()` - only admins can update presentations
    - Implement `delete()` - only admins can delete presentations
    - _Requirements: 1.4, 1.5, 2.1, 3.5, 6.1_
  
  - [~] 6.2 Register policy in AuthServiceProvider
    - Add Presentation::class => PresentationPolicy::class to $policies array
    - _Requirements: 1.4_
  
  - [ ]* 6.3 Write property test for authorization enforcement
    - **Property 1: Authorization Enforcement**
    - **Validates: Requirements 1.4, 1.5**
  
  - [ ]* 6.4 Write unit tests for policy methods
    - Test admin users pass all policy checks
    - Test regular users fail create/update/delete checks
    - Test regular users pass view checks
    - _Requirements: 1.4, 1.5_

- [~] 7. Checkpoint - Verify backend foundation
  - Ensure all tests pass, verify database schema is correct, test Cloudinary connection manually if needed. Ask the user if any questions arise before proceeding to controllers.

- [ ] 8. Implement PresentationController
  - [~] 8.1 Create PresentationController with index method
    - Implement `index()` method that retrieves paginated presentations (20 per page)
    - Eager load uploader relationship
    - Order by created_at descending
    - Return Inertia response with 'Presentations/Index' component
    - Pass presentations data and auth user
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [~] 8.2 Implement create and store methods
    - Implement `create()` method (admin only) that returns Inertia response with 'Presentations/Create' component
    - Implement `store()` method that accepts PresentationStoreRequest
    - Call PresentationService to create presentation
    - Handle upload errors with try-catch and return appropriate error messages
    - Redirect to presentations.index with success message on completion
    - _Requirements: 2.1, 2.2, 2.4, 2.5, 2.7_
  
  - [~] 8.3 Implement show method
    - Implement `show()` method that loads single presentation with uploader relationship
    - Return Inertia response with 'Presentations/Show' component
    - Pass presentation data
    - _Requirements: 5.1_
  
  - [~] 8.4 Implement edit and update methods
    - Implement `edit()` method (admin only) that returns Inertia response with 'Presentations/Edit' component
    - Implement `update()` method that accepts PresentationUpdateRequest
    - Call PresentationService to update metadata
    - Redirect back with success message
    - _Requirements: 3.5_
  
  - [~] 8.5 Implement destroy method
    - Implement `destroy()` method (admin only) that accepts Presentation model
    - Call PresentationService to delete presentation
    - Handle Cloudinary deletion errors gracefully with logging
    - Redirect to presentations.index with success message
    - _Requirements: 6.2, 6.3, 6.4, 6.5_
  
  - [ ]* 8.6 Write property test for presentation deletion completeness
    - **Property 11: Presentation Deletion Completeness**
    - **Validates: Requirements 6.4**
  
  - [ ]* 8.7 Write integration tests for controller methods
    - Test index displays paginated presentations
    - Test store creates presentation with valid data
    - Test store rejects invalid data
    - Test show displays single presentation
    - Test update modifies metadata
    - Test destroy removes presentation
    - _Requirements: 2.4, 4.1, 5.1, 6.4_

- [~] 9. Configure routes with middleware protection
  - Define routes in `routes/web.php`
  - Group presentation routes under auth middleware
  - Apply policy middleware to admin-only routes (create, store, edit, update, destroy)
  - Use resourceful routing where appropriate
  - _Requirements: 1.4, 1.5, 2.1_

- [ ] 10. Create shared TypeScript types
  - [~] 10.1 Create types/index.ts file
    - Define User interface with all fields including role type
    - Define Presentation interface with all metadata fields
    - Define PaginatedResponse<T> generic interface
    - Define PaginationLink interface
    - Define PageProps interface for Inertia page props
    - _Requirements: All frontend requirements_
  
  - [~] 10.2 Configure TypeScript path aliases
    - Update tsconfig.json with path mappings for @/Components, @/Pages, @/types
    - _Requirements: All frontend requirements_

- [ ] 11. Implement Presentation Index page
  - [~] 11.1 Create PresentationCard component
    - Accept presentation prop with TypeScript interface
    - Display title, description (truncated), file size, upload date
    - Show uploader name
    - Show admin actions (edit, delete) only if user isAdmin
    - Emit delete event when delete clicked
    - Style with Tailwind CSS
    - _Requirements: 4.1, 4.2, 6.1_
  
  - [~] 11.2 Create DeleteConfirmationDialog component
    - Use Reka UI Dialog component
    - Accept presentation title as prop
    - Show confirmation message with presentation title
    - Emit confirm and cancel events
    - _Requirements: 6.2_
  
  - [~] 11.3 Create Presentations/Index.vue page
    - Accept presentations (PaginatedResponse) prop
    - Display grid of PresentationCard components
    - Implement pagination controls using Inertia links
    - Show "Upload Presentation" button for admin users
    - Handle delete action with confirmation dialog
    - Submit delete request via Inertia DELETE
    - Display toast notifications for success/error
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_
  
  - [ ]* 11.4 Write property test for presentation listing completeness
    - **Property 7: Presentation Listing Completeness**
    - **Validates: Requirements 4.1, 4.2**
  
  - [ ]* 11.5 Write property test for presentation sorting order
    - **Property 8: Presentation Sorting Order**
    - **Validates: Requirements 4.3**
  
  - [ ]* 11.6 Write property test for pagination logic
    - **Property 9: Pagination Logic**
    - **Validates: Requirements 4.4, 4.5**

- [ ] 12. Implement Presentation Upload page
  - [~] 12.1 Create UploadProgress component
    - Accept progress percentage prop
    - Display progress bar with percentage
    - Style with Tailwind CSS
    - _Requirements: 9.2_
  
  - [~] 12.2 Create Presentations/Create.vue page
    - Use Inertia form helper
    - Add file input with accept attribute for .ppt,.pptx
    - Add title input (required)
    - Add description textarea (optional, max 1000 chars)
    - Validate file type and size on client side before submission
    - Show file name and size after selection
    - Display UploadProgress component during upload
    - Submit via Inertia POST with progress tracking
    - Display validation errors inline
    - Display toast notification on success/error
    - Redirect to index on success
    - _Requirements: 2.1, 2.2, 2.5, 3.1, 3.2, 9.2_

- [ ] 13. Implement Presentation Viewer page
  - [~] 13.1 Create PresentationViewer component
    - Accept cloudinaryPublicId prop
    - Generate Cloudinary delivery URL with f_auto and q_auto parameters
    - Embed presentation using appropriate method (iframe, image gallery, or download link based on Cloudinary capabilities)
    - Include slide navigation controls if using image gallery approach
    - Style viewer container with Tailwind CSS
    - _Requirements: 5.2, 5.3, 5.4, 5.7_
  
  - [~] 13.2 Create Presentations/Show.vue page
    - Accept presentation prop
    - Display presentation title at top
    - Render PresentationViewer component with cloudinary_public_id
    - Display presentation description below viewer
    - Include back button to return to presentation library
    - Show presentation metadata (upload date, file size, uploader)
    - _Requirements: 5.1, 5.3, 5.6_
  
  - [ ]* 13.3 Write property test for Cloudinary URL generation
    - **Property 10: Cloudinary URL Generation**
    - **Validates: Requirements 5.3, 5.7, 8.3**

- [ ] 14. Implement Presentation Edit page
  - [~] 14.1 Create Presentations/Edit.vue page
    - Accept presentation prop
    - Use Inertia form helper pre-filled with current title and description
    - Add title input (required)
    - Add description textarea (optional, max 1000 chars)
    - Submit via Inertia PUT
    - Display validation errors inline
    - Display toast notification on success/error
    - Redirect back to show page on success
    - _Requirements: 3.5_

- [ ] 15. Implement shared layout and navigation
  - [~] 15.1 Update AppLayout component
    - Add navigation link to "Presentations" (always visible to authenticated users)
    - Add "Upload Presentation" link in navigation (visible only to admins)
    - Display user name and role in header
    - Include logout button
    - Apply consistent styling with Tailwind CSS
    - _Requirements: 7.6_
  
  - [~] 15.2 Implement responsive design
    - Ensure all components are mobile-responsive
    - Use Tailwind responsive utilities (sm:, md:, lg:)
    - Test on mobile, tablet, and desktop viewports
    - _Requirements: 7.2_
  
  - [~] 15.3 Add loading indicators
    - Use Inertia's progress indicator for page transitions
    - Configure Inertia progress bar in app.ts
    - Add loading state to upload form during file upload
    - _Requirements: 7.4_
  
  - [~] 15.4 Configure toast notifications
    - Set up vue-sonner for toast notifications
    - Display flash messages from backend as toasts
    - Use success toasts for successful operations
    - Use error toasts for failed operations
    - _Requirements: 7.5_

- [~] 16. Apply consistent styling and UI polish
  - Ensure all pages use consistent color scheme and typography
  - Apply hover and focus states to all interactive elements
  - Ensure proper spacing and layout using Tailwind CSS
  - Verify all components use Reka UI components where appropriate (buttons, dialogs, cards)
  - Test UI in light and dark modes if theme switching is implemented
  - _Requirements: 7.1, 7.3, 7.7_

- [~] 17. Checkpoint - Verify frontend integration
  - Test complete user flow from login to upload to viewing
  - Verify admin features are visible only to admins
  - Test responsive design on different screen sizes
  - Ensure all error messages display correctly
  - Ask the user if any questions arise before final integration.

- [ ] 18. Implement error handling and logging
  - [~] 18.1 Configure logging channels
    - Add cloudinary and uploads channels to `config/logging.php`
    - Configure daily rotation for log files
    - _Requirements: All error handling requirements_
  
  - [~] 18.2 Add error handling to controllers
    - Wrap Cloudinary operations in try-catch blocks
    - Log upload failures with context (user_id, filename, error)
    - Log deletion failures with context (presentation_id, public_id, error)
    - Return user-friendly error messages
    - _Requirements: 2.4, 6.5_
  
  - [~] 18.3 Add global error handler to frontend
    - Configure Vue error handler in app.ts
    - Display network errors with toast notifications
    - Log errors to console for debugging
    - _Requirements: 7.5_

- [ ] 19. Security hardening
  - [~] 19.1 Configure PHP upload limits
    - Update `php.ini` or `.htaccess` to set upload_max_filesize=50M and post_max_size=52M
    - Document required PHP configuration
    - _Requirements: 2.5_
  
  - [~] 19.2 Implement rate limiting
    - Apply throttle middleware to upload route (e.g., max 10 uploads per hour)
    - Apply throttle middleware to authentication routes
    - _Requirements: 10.7_
  
  - [~] 19.3 Verify CSRF and XSS protection
    - Confirm CSRF middleware is active on all POST/PUT/DELETE routes
    - Verify Vue templates use proper escaping ({{ }})
    - Audit any v-html usage for XSS vulnerabilities
    - _Requirements: 10.6, 10.7_

- [ ] 20. Database seeding and factories
  - [~] 20.1 Create PresentationFactory
    - Generate random presentation data using Faker
    - Create realistic file sizes, titles, descriptions
    - Generate fake Cloudinary public_ids and URLs
    - _Requirements: All testing requirements_
  
  - [~] 20.2 Create AdminUserSeeder
    - Create admin user with known credentials for testing
    - Create a few regular users for testing
    - _Requirements: 1.3_
  
  - [~] 20.3 Create PresentationSeeder
    - Seed database with sample presentations using factory
    - Create presentations from both admin and regular users
    - _Requirements: All testing requirements_

- [ ]* 21. Write integration tests for complete user flows
  - Test complete upload flow: login as admin → navigate to upload → select file → fill form → submit → verify in database and Cloudinary
  - Test complete viewing flow: login as user → navigate to library → click presentation → view presentation
  - Test complete deletion flow: login as admin → navigate to library → delete presentation → verify removed from database and Cloudinary
  - Test authorization flow: login as regular user → attempt to access upload page → verify denied
  - _Requirements: All requirements_

- [ ] 22. Performance optimization
  - [~] 22.1 Implement eager loading
    - Ensure uploader relationship is eager loaded in index and show queries
    - _Requirements: 9.3_
  
  - [~] 22.2 Add query result caching
    - Cache presentation library queries for 5 minutes
    - Clear cache on create/update/delete
    - _Requirements: 9.3_
  
  - [~] 22.3 Configure Inertia for optimal performance
    - Set up Inertia partial reloads for list updates
    - Configure asset versioning for cache busting
    - _Requirements: 9.1, 9.4_

- [ ] 23. Documentation and deployment preparation
  - [~] 23.1 Update README with setup instructions
    - Document environment variable requirements
    - List PHP and Node.js version requirements
    - Provide step-by-step setup instructions
    - Document Cloudinary configuration
    - _Requirements: All requirements_
  
  - [~] 23.2 Create .env.example
    - Include all required environment variables with example values
    - Add comments explaining each variable
    - _Requirements: 2.9, 8.6_
  
  - [~] 23.3 Prepare deployment commands
    - Document production optimization commands (config:cache, route:cache, view:cache, npm run build)
    - Document queue worker setup for async upload processing (if implemented)
    - _Requirements: All requirements_

- [~] 24. Final checkpoint - Complete system verification
  - Run full test suite and ensure all tests pass
  - Perform manual end-to-end testing of all features
  - Verify admin and regular user permissions work correctly
  - Test upload with various file types and sizes
  - Verify Cloudinary integration works end-to-end
  - Check responsive design on multiple devices
  - Review error messages for clarity and helpfulness
  - Ask the user if the implementation is complete or if any adjustments are needed.

## Notes

- Tasks marked with `*` are optional property-based tests and can be skipped for faster MVP delivery
- Each task references specific requirements from the requirements document for traceability
- Checkpoints are included at reasonable breaks to ensure incremental validation
- Property tests validate universal correctness properties across many generated inputs
- Unit and integration tests validate specific examples, edge cases, and integration points
- The implementation follows Laravel and Vue.js best practices with proper separation of concerns
- Security features (malware scanning, filename sanitization, authorization) are integrated throughout
- The plan assumes Cloudinary account is already set up with the provided credentials

