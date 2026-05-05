## 1. Backend - Progress Reporting

- [ ] 1.1 Locate and read current Dashboard Livewire component with syncChannels method
- [ ] 1.2 Add `is_importing` boolean property to track import state
- [ ] 1.3 Add `import_progress` object with: current_channel, current_logo, processed, total, percentage
- [ ] 1.4 Modify syncChannels to set is_importing=true before starting
- [ ] 1.5 Add progress dispatch after processing each channel (every 1 channel or batch)
- [ ] 1.6 Modify syncChannels to set is_importing=false after completion
- [ ] 1.7 Run pint to format changes

## 2. Frontend - Progress Bar Component

- [ ] 2.1 Add import progress state properties to dashboard.blade.php
- [ ] 2.2 Create progress bar HTML in dashboard (replace bottom channel grid)
- [ ] 2.3 Style progress bar with Tailwind CSS (percentage, channel info, progress bar)
- [ ] 2.4 Add conditional display: show progress when is_importing=true
- [ ] 2.5 Run npm run build to verify frontend compiles

## 3. Frontend - Remove Bottom Channel Grid

- [ ] 3.1 Locate the "Todos os Canais" channel grid section in dashboard.blade.php
- [ ] 3.2 Add conditional: hide grid when is_importing=true
- [ ] 3.3 Show empty container when import not running (not the channel grid)
- [ ] 3.4 Verify layout still works with sidebar only

## 4. Frontend - Channel Info Display

- [ ] 4.1 Add current channel name display in progress panel
- [ ] 4.2 Add current channel logo in progress panel
- [ ] 4.3 Add processed/total count display
- [ ] 4.4 Style channel info section in progress panel

## 5. Testing

- [ ] 5.1 Test import with small M3U (5-10 channels)
- [ ] 5.2 Test import with larger M3U to verify progress updates
- [ ] 5.3 Verify UI state after import completes (progress bar hides)
- [ ] 5.4 Run `vendor/bin/sail artisan test --compact` to verify no regressions
