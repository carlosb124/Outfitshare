```mermaid
erDiagram
    USER ||--o{ PRENDA : "gestiona (1:N)"
    USER ||--o{ OUTFIT : "crea (1:N)"
    USER ||--o{ LIKE : "realiza (1:N)"
    OUTFIT ||--o{ LIKE : "recibe (1:N)"
    OUTFIT }|--|{ PRENDA : "contiene (N:M)"

    USER {
        int id PK
        string email UK
        string password
        string name
        json roles
        int puntos
        boolean isBanned
    }

    PRENDA {
        int id PK
        string nombre
        string imagen
        string marca
        string categoria
        int user_id FK
    }

    OUTFIT {
        int id PK
        string titulo
        text descripcion
        datetime fechaPublicacion
        int user_id FK
    }

    LIKE {
        int id PK
        int user_id FK
        int outfit_id FK
    }
    
