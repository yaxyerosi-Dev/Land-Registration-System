# LandReg Pro - Entity Relationship Diagram (ERD)

## Mermaid ERD

```mermaid
erDiagram
    users ||--o| owners : "has profile"
    owners ||--o{ properties : "owns"
    lands ||--o{ properties : "contains"
    properties ||--o{ ownership_transfers : "transferred via"
    properties ||--o{ certificates : "has"
    users ||--o{ notifications : "receives"
    users ||--o{ audit_logs : "performs"
    owners ||--o{ ownership_transfers : "current owner"
    owners ||--o{ ownership_transfers : "new owner"

    users {
        int id PK
        string full_name
        string email UK
        string phone
        string password
        enum role
        enum status
        string profile_photo
        timestamp created_at
    }

    owners {
        int id PK
        int user_id FK
        string full_name
        string national_id UK
        string phone
        string email
        text address
        string photo
        date registration_date
        timestamp created_at
    }

    lands {
        int id PK
        string plot_number UK
        string land_number UK
        string region
        string district
        string neighborhood
        text full_address
        string land_size
        enum land_type
        date registration_date
        enum status
        timestamp created_at
    }

    properties {
        int id PK
        string property_number UK
        string registration_number UK
        int owner_id FK
        int land_id FK
        date ownership_date
        enum status
        timestamp created_at
    }

    ownership_transfers {
        int id PK
        int property_id FK
        int current_owner_id FK
        int new_owner_id FK
        text transfer_reason
        date transfer_date
        enum status
        text admin_remark
        timestamp created_at
    }

    certificates {
        int id PK
        int property_id FK
        string certificate_number UK
        string qr_code
        date issue_date
        enum status
        timestamp created_at
    }

    notifications {
        int id PK
        int user_id FK
        string title
        text message
        enum type
        tinyint is_read
        timestamp created_at
    }

    settings {
        int id PK
        string system_name
        string office_name
        text office_address
        string office_phone
        string office_email
        string logo
        timestamp created_at
    }

    audit_logs {
        int id PK
        int user_id FK
        string action
        text description
        timestamp created_at
    }
```

## Relationships Summary

| Relationship | Cardinality | Description |
|-------------|-------------|-------------|
| users → owners | 1:0..1 | A user account may link to one owner profile |
| owners → properties | 1:N | An owner can hold multiple properties |
| lands → properties | 1:N | A land parcel can have property records |
| properties → certificates | 1:N | Each property can have certificate history |
| properties → transfers | 1:N | Transfer requests per property |
