## ADDED Requirements

### Requirement: Search channels by name
The system SHALL filter channels by name as user types.

#### Scenario: Search by name
- **WHEN** user types in the search input
- **THEN** system filters channels where `name` contains the search term (case-insensitive)

#### Scenario: Clear search
- **WHEN** user clears the search input
- **THEN** system shows all channels

### Requirement: Filter by group
The system SHALL filter channels by category/group.

#### Scenario: Filter by group
- **WHEN** user selects a group from dropdown
- **THEN** system shows only channels where `group` matches selected group

#### Scenario: All groups
- **WHEN** user selects "Todos" in group dropdown
- **THEN** system shows all channels (no group filter)

#### Scenario: Group dropdown population
- **WHEN** dashboard loads
- **THEN** system populates group dropdown with unique values from `channels.group`
- **AND** includes "Todos" as first option