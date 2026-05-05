## 1. Database Setup

- [x] 1.1 Create migration for `channels` table (id, name, logo, group, stream_url, is_active, timestamps)
- [x] 1.2 Create migration for `settings` table (id, key, value, timestamps)
- [x] 1.3 Create Channel model with $fillable and is_active scope
- [x] 1.4 Create Setting model with key-value find/set methods

## 2. M3U Parser Service

- [x] 2.1 Create M3UParser service class
- [x] 2.2 Implement fetchFromUrl() method using Http facade
- [x] 2.3 Implement parse() method for EXTINF format extraction
- [x] 2.4 Implement syncToDatabase() method with update/create logic

## 3. Livewire Components

- [x] 3.1 Create Settings livewire component (form with m3u_url input, save method)
- [x] 3.2 Create Dashboard livewire component (main layout, state management)
- [x] 3.3 Create ChannelList livewire component (sidebar list with toggle)
- [x] 3.4 Create Player livewire component (hls.js integration)

## 4. Views and Layout

- [x] 4.1 Create Blade view for Settings modal
- [x] 4.2 Create Blade view for Dashboard layout
- [x] 4.3 Create Blade view for ChannelList sidebar
- [x] 4.4 Create Blade view for Player with fullscreen button
- [x] 4.5 Update welcome.blade.php with main layout structure
- [x] 4.6 Add sidebar toggle CSS and JavaScript

## 5. Routing and Integration

- [x] 5.1 Update web.php to render Dashboard as main page
- [x] 5.2 Add Settings route for modal
- [x] 5.3 Include hls.js via CDN

## 6. Search and Filter

- [x] 6.1 Add search input to Dashboard with debounce
- [x] 6.2 Add group filter dropdown populated from database
- [x] 6.3 Implement Livewire property for search term and selected group
- [x] 6.4 Add scopes to Channel model for filtering