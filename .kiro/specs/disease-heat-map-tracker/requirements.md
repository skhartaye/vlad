# Requirements Document

## Introduction

The Disease Heat Map Tracker is a web-based system that monitors and visualizes the geographic distribution of three diseases: leptospirosis, dengue, and malaria. The system allows authenticated patients to report their location, which is then displayed on an interactive heat map. Guest users can view the heat map without authentication to identify and avoid high-risk areas.

## Glossary

- **System**: The Disease Heat Map Tracker web application
- **Patient User**: An authenticated user who has been diagnosed with one of the tracked diseases and can submit location data
- **Guest User**: An unauthenticated visitor who can view the heat map but cannot submit data
- **Heat Map**: A geographic visualization showing disease concentration by area using color intensity
- **Case Report**: A record containing disease type, patient location, and timestamp
- **XAMPP**: Local development environment providing Apache, MySQL, and PHP
- **CRUD Operations**: Create, Read, Update, Delete database operations

## Requirements

### Requirement 1

**User Story:** As a patient user, I want to register and log in to the system, so that I can report my disease case and location.

#### Acceptance Criteria

1. THE System SHALL provide a registration form that accepts username, password, email, and disease type
2. WHEN a patient user submits valid registration credentials, THE System SHALL create a new user account in the database
3. THE System SHALL provide a login form that accepts username and password
4. WHEN a patient user submits valid login credentials, THE System SHALL authenticate the user and create a session
5. IF login credentials are invalid, THEN THE System SHALL display an error message and deny access

### Requirement 2

**User Story:** As a patient user, I want to submit my address and have it pinned on the map, so that health authorities and others can see disease distribution.

#### Acceptance Criteria

1. WHEN a patient user is authenticated, THE System SHALL display a case submission form with address input field
2. THE System SHALL accept address input in text format from the patient user
3. WHEN a patient user submits an address, THE System SHALL geocode the address to latitude and longitude coordinates
4. WHEN geocoding is successful, THE System SHALL store the case report with disease type, coordinates, and timestamp in the database
5. IF geocoding fails, THEN THE System SHALL display an error message and prompt the patient user to enter a valid address

### Requirement 3

**User Story:** As a patient user, I want to view my submitted case reports, so that I can verify my data has been recorded correctly.

#### Acceptance Criteria

1. WHEN a patient user is authenticated, THE System SHALL display a list of all case reports submitted by that patient user
2. THE System SHALL display each case report with disease type, address, and submission date
3. THE System SHALL allow the patient user to update their submitted case reports
4. THE System SHALL allow the patient user to delete their submitted case reports

### Requirement 4

**User Story:** As a guest user, I want to view the disease heat map without logging in, so that I can identify and avoid high-risk areas.

#### Acceptance Criteria

1. THE System SHALL provide a guest access option on the home page that does not require authentication
2. WHEN a guest user selects guest access, THE System SHALL display the interactive heat map
3. THE System SHALL prevent guest users from accessing case submission functionality
4. THE System SHALL allow guest users to view the heat map with the same visualization features as authenticated users

### Requirement 5

**User Story:** As any user (patient or guest), I want to see an interactive heat map showing disease concentration, so that I can understand which areas have high case numbers.

#### Acceptance Criteria

1. THE System SHALL display an interactive map using a mapping library
2. THE System SHALL retrieve all case reports from the database for heat map generation
3. THE System SHALL generate a heat map layer that visualizes disease concentration using color intensity
4. THE System SHALL allow users to filter the heat map by disease type (leptospirosis, dengue, malaria)
5. WHEN multiple cases exist in close proximity, THE System SHALL increase the heat intensity for that geographic area

### Requirement 6

**User Story:** As any user, I want the heat map to update with current data, so that I can see the most recent disease distribution.

#### Acceptance Criteria

1. WHEN a patient user submits a new case report, THE System SHALL add the new data point to the heat map
2. THE System SHALL display case reports with timestamps within the last 90 days on the heat map
3. THE System SHALL provide a refresh mechanism to reload heat map data
4. WHEN a patient user deletes a case report, THE System SHALL remove that data point from the heat map

### Requirement 7

**User Story:** As a system administrator, I want the application to use XAMPP with MySQL database, so that case data is persistently stored and manageable.

#### Acceptance Criteria

1. THE System SHALL connect to a MySQL database running on XAMPP
2. THE System SHALL create database tables for users, case reports, and disease types
3. THE System SHALL implement CRUD operations for user accounts
4. THE System SHALL implement CRUD operations for case reports
5. THE System SHALL handle database connection errors and display appropriate error messages

### Requirement 8

**User Story:** As a patient user, I want my password to be securely stored, so that my account is protected.

#### Acceptance Criteria

1. WHEN a patient user registers, THE System SHALL hash the password before storing it in the database
2. THE System SHALL use a secure hashing algorithm (bcrypt or similar) for password storage
3. WHEN a patient user logs in, THE System SHALL verify the password against the stored hash
4. THE System SHALL not display or transmit passwords in plain text

### Requirement 9

**User Story:** As any user, I want the system to have a clear navigation structure, so that I can easily access different features.

#### Acceptance Criteria

1. THE System SHALL display a navigation bar on all pages
2. THE System SHALL provide navigation links to home, heat map, login, and registration pages
3. WHEN a patient user is authenticated, THE System SHALL display additional navigation links for case submission and user profile
4. WHEN a patient user logs out, THE System SHALL destroy the session and redirect to the home page
