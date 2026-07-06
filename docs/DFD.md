# LandReg Pro - Data Flow Diagrams (DFD)

## DFD Level 0 (Context Diagram)

```mermaid
flowchart LR
    Admin[Administrator]
    User[Property Owner]
    Public[Public Visitor]

    subgraph System["LandReg Pro System"]
        S((Land Registration<br/>Management System))
    end

    DB[(MySQL Database<br/>landreg_pro)]
    Files[/File Storage<br/>Uploads & QR/]

    Admin -->|Manage records, approve transfers,<br/>reports, settings| S
    S -->|Dashboard, reports,<br/>notifications| Admin

    User -->|Register, login, view properties,<br/>request transfers| S
    S -->|Properties, certificates,<br/>notifications| User

    Public -->|Certificate number / QR| S
    S -->|Verification result| Public

    S <-->|CRUD operations| DB
    S <-->|Photos, logos, QR codes| Files
```

## DFD Level 1

```mermaid
flowchart TB
    Admin[Administrator]
    User[Property Owner]
    Public[Public Visitor]

    subgraph Processes
        P1[1.0 Authentication<br/>& Authorization]
        P2[2.0 Owner & User<br/>Management]
        P3[3.0 Land & Property<br/>Management]
        P4[4.0 Transfer<br/>Processing]
        P5[5.0 Certificate<br/>Generation]
        P6[6.0 Search &<br/>Verification]
        P7[7.0 Reports &<br/>Export]
        P8[8.0 Notifications]
        P9[9.0 Settings &<br/>Audit Logs]
    end

    D1[(D1: users)]
    D2[(D2: owners)]
    D3[(D3: lands)]
    D4[(D4: properties)]
    D5[(D5: transfers)]
    D6[(D6: certificates)]
    D7[(D7: notifications)]
    D8[(D8: settings)]
    D9[(D9: audit_logs)]
    D10[/D10: uploads/]

    Admin --> P1
    Admin --> P2
    Admin --> P3
    Admin --> P4
    Admin --> P7
    Admin --> P8
    Admin --> P9
    User --> P1
    User --> P4
    User --> P8
    Public --> P6

    P1 <--> D1
    P2 <--> D1
    P2 <--> D2
    P3 <--> D2
    P3 <--> D3
    P3 <--> D4
    P3 --> P5
    P4 <--> D4
    P4 <--> D5
    P4 --> P5
    P5 <--> D4
    P5 <--> D6
    P5 <--> D10
    P6 <--> D4
    P6 <--> D6
    P7 <--> D3
    P7 <--> D2
    P7 <--> D4
    P7 <--> D5
    P7 <--> D6
    P8 <--> D7
    P9 <--> D8
    P9 <--> D9
    P9 <--> D10
```

## Data Store Descriptions

| Store | Contents |
|-------|----------|
| D1: users | Account credentials, roles, profile photos |
| D2: owners | Property owner personal details |
| D3: lands | Land parcel geographic and type data |
| D4: properties | Ownership records linking owners and lands |
| D5: transfers | Transfer requests and approval status |
| D6: certificates | Issued certificates with QR references |
| D7: notifications | User notification messages |
| D8: settings | System and office configuration |
| D9: audit_logs | Activity trail for accountability |
| D10: uploads | Physical files (photos, logos, QR images) |
