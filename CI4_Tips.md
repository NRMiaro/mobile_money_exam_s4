# CodeIgniter 4 - Guide Complet

**Table des matières**
1. [Arborescence & Structure](#1-arborescence--structure)
2. [Routing & Controllers](#2-routing--controllers)
3. [Models & Database](#3-models--database)
4. [Service Layer](#4-service-layer)
5. [Validation & Erreurs](#5-validation--gestion-derreurs)
6. [Migrations & Seeds](#6-migrations--seeds)
7. [Configuration](#7-configuration)
8. [Sécurité](#8-sécurité)
9. [Patterns Avancés](#9-patterns-avancés)
10. [Debugging & Optimisation](#10-debugging--optimisation)

---

## 1. Arborescence & Structure

### Structure Standard

```
app/
├── Controllers/              # Contrôleurs
│   ├── BaseController.php   # Parent - commun à tous
│   ├── Home.php
│   └── Admin/
├── Models/                   # Interaction BD + logique données
│   └── UserModel.php
├── Views/                    # Templates (PHP ou Twig)
│   └── users/
│       ├── index.php
│       └── show.php
├── Services/                 # Logique métier (optionnel mais recommandé)
│   └── UserService.php
├── Filters/                  # Middleware (avant/après requête)
│   └── AuthFilter.php
├── Libraries/                # Classes custom réutilisables
├── Config/                   # Configuration app
│   ├── Routes.php
│   ├── Database.php
│   ├── Services.php
│   └── App.php
├── Database/
│   ├── Migrations/           # Schéma versionnée (ALTER/CREATE TABLE)
│   │   ├── 2024-01-10-000001_CreateUsersTable.php
│   │   └── 2024-01-15-000002_AddVerificationToUsers.php
│   └── Seeds/                # Données initiales
│       ├── UserSeeder.php
│       └── DatabaseSeeder.php
└── Entities/                 # Value Objects (optionnel)

public/
├── index.php                 # Entry point (point entrée)
├── .htaccess                 # Rewrite rules (Apache)
├── css/
├── js/
└── uploads/

writable/
├── logs/                      # Logs applicatifs
├── cache/                     # Cache fichiers
└── uploads/                   # Fichiers temporaires

tests/                         # Tests unitaires & fonctionnels
```

### Points Clés

- **BaseController** : Tous les contrôleurs hériteront de cette classe pour bénéficier de propriétés communes (`$request`, `$response`, `$session`, `$validator`, etc.)
- **app/Config/** : Chaque fichier retourne un objet de configuration
- **APPPATH** : Constante qui pointe vers `app/`
- **PUBLICPATH** : Constante qui pointe vers `public/`

**Exemple accès constants** :
```php
// Dans n'importe quel fichier
echo APPPATH . 'Controllers/';      // app/Controllers/
echo PUBLICPATH . 'css/style.css';   // public/css/style.css
```

---

## 2. Routing & Controllers

### Routes Basiques

Fichier : `app/Config/Routes.php`

```php
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Route simple : méthode HTTP → Contrôleur::Méthode
$routes->get('/', 'Home::index');
$routes->post('/users', 'UserController::store');
$routes->get('/users/(:num)', 'UserController::show/$1');
$routes->put('/users/(:num)', 'UserController::update/$1');
$routes->delete('/users/(:num)', 'UserController::delete/$1');
```

**Explications** :
- `(:num)` capture un nombre uniquement → `$1` l'injecte en paramètre
- `(:any)` capture n'importe quel caractère
- `(:alpha)` capture uniquement lettres
- `(:alphanum)` capture lettres + chiffres

### Segments Multiples

```php
// /posts/slug-du-post/comments/123
$routes->get('/posts/(:any)/comments/(:num)', 'PostController::comment/$1/$2');

// La méthode recevra : comment($slug, $commentId)
```

### Groups & Préfixes

```php
// Toutes les routes commencent par /api
$routes->group('api', static function($routes) {
    $routes->post('users', 'Api\UserController::store');
    $routes->get('users/(:num)', 'Api\UserController::show/$1');
});

// URL résultante : /api/users, /api/users/1
```

### Filters (Middleware)

Exécute du code **avant** l'action du contrôleur :

```php
// Route avec filter
$routes->get('/admin/dashboard', 'Admin\Dashboard::index', ['filter' => 'isAdmin']);

// Filter utilisable sur plusieurs routes
$routes->group('admin', ['filter' => 'isAdmin'], static function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('users', 'Admin\Users::index');
});
```

Implémenter un filter : `app/Filters/IsAdminFilter.php`
```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IsAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Vérifier si utilisateur est admin
        if (!service('auth')->isAdmin()) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'Access denied']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Exécuté après la réponse (rarement utilisé)
    }
}
```

Enregistrer le filter : `app/Config/Filters.php`
```php
public array $aliases = [
    'isAdmin' => \App\Filters\IsAdminFilter::class,
    'cors'    => \App\Filters\CorsFilter::class,
];
```

### Resource Routing (CRUD automatique)

```php
// Génère automatiquement les 7 routes RESTful
$routes->resource('posts');

// Crée automatiquement :
// GET    /posts              → index()      (lister tous)
// GET    /posts/new          → new()        (form création)
// POST   /posts              → create()     (enregistrer)
// GET    /posts/1            → show($id)    (voir un)
// GET    /posts/1/edit       → edit($id)    (form édition)
// PUT    /posts/1            → update($id)  (update)
// DELETE /posts/1            → delete($id)  (supprimer)
```

### Contrôleur Basique

```php
<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected UserModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
    }

    /**
     * Liste tous les utilisateurs
     * GET /users
     */
    public function index(): ResponseInterface
    {
        $users = $this->model->findAll();
        return $this->response->setJSON($users);
    }

    /**
     * Récupère un utilisateur spécifique
     * GET /users/1
     */
    public function show($id = null): ResponseInterface
    {
        $user = $this->model->find($id);
        
        if (!isset($user)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        return $this->response->setJSON($user);
    }

    /**
     * Crée un nouvel utilisateur
     * POST /users
     */
    public function store(): ResponseInterface
    {
        // getJSON(true) = array associatif
        $data = $this->request->getJSON(true);
        
        // Validation automatique du model
        if (!$this->model->save($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'errors' => $this->model->errors()
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'id' => $this->model->getInsertID()
        ]);
    }

    /**
     * Met à jour un utilisateur
     * PUT /users/1
     */
    public function update($id = null): ResponseInterface
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->update($id, $data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'errors' => $this->model->errors()
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON(['success' => true]);
    }

    /**
     * Supprime un utilisateur
     * DELETE /users/1
     */
    public function delete($id = null): ResponseInterface
    {
        if (!$this->model->delete($id)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        return $this->response->setStatusCode(204);
    }
}
?>
```

### Propriétés BaseController

Disponibles dans **tous** les contrôleurs :

```php
// Requête HTTP
$this->request->getVar('name');              // GET ou POST
$this->request->getPost('email');            // POST uniquement
$this->request->getGet('page');              // GET uniquement
$this->request->getJSON(true);               // Body JSON → array
$this->request->getHeaderLine('Authorization');  // Headers

// Réponse HTTP
$this->response->setStatusCode(200);
$this->response->setJSON(['data' => $user]);
$this->response->setHeader('Content-Type', 'application/json');

// Session
$this->session->get('user_id');
$this->session->set('cart', $items);
$this->session->destroy();

// Validation
$this->validate(['email' => 'required|valid_email']);
$this->validator->getErrors();

// Views
view('users/index', ['users' => $users]);
```

---

## 3. Models & Database

### Model CI4

Fichier : `app/Models/UserModel.php`

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // ========== CONFIGURATION ==========
    
    protected $table = 'users';           // Table BD associée
    protected $primaryKey = 'id';          // Clé primaire
    protected $useTimestamps = true;         // Auto created_at/updated_at
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';    // Format timestamps

    // ========== MASS ASSIGNMENT ==========
    
    // Colonnes autorisées pour insert/update (sécurité)
    protected $allowedFields = ['name', 'email', 'password', 'role'];

    // ========== CASTING AUTOMATIQUE ==========
    
    // Cast automatiquement colonnes à certains types
    protected $casts = [
        'id' => 'int',
        'email' => 'string',
        'is_active' => 'boolean',
        'metadata' => 'json',          // Convertit JSON string ↔ array
        'created_at' => 'datetime',    // Convertit datetime
    ];

    // ========== VALIDATION ==========
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]|alpha_space',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[admin,user,moderator]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Cette adresse email est déjà utilisée.',
            'valid_email' => 'Veuillez entrer une adresse email valide.',
        ],
    ];

    // ========== CALLBACKS ==========
    
    // Callback = fonction exécutée avant/après insert/update/delete
    protected $beforeInsert = ['hashPassword'];
    protected $afterFind = ['unhashPassword'];
    protected $afterUpdate = ['logUpdate'];

    /**
     * Hash le password avant insertion
     * Appelé automatiquement par save() / insert()
     */
    public function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash(
                $data['data']['password'],
                PASSWORD_BCRYPT
            );
        }
        return $data;
    }

    /**
     * Callback après find (findAll, find, etc.)
     * Utile pour transformer données
     */
    public function unhashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            unset($data['data']['password']);  // Masquer password en sortie
        }
        return $data;
    }

    /**
     * Callback après update
     */
    public function logUpdate(array $data)
    {
        log_msg('info', 'User ' . $data['id'] . ' updated');
        return $data;
    }

    // ========== REQUÊTES CUSTOM ==========

    /**
     * Trouver utilisateur par email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Lister utilisateurs actifs
     */
    public function getActive(int $limit = 20): array
    {
        return $this->where('is_active', true)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
?>
```

### Requêtes Courantes

```php
$model = new UserModel();

// ========== SELECT ==========

// Tous les records
$users = $model->findAll();

// Trouver par clé primaire (id)
$user = $model->find(1);                    // Un record
$users = $model->find([1, 2, 3]);           // Plusieurs records

// WHERE clause
$user = $model->where('email', 'john@example.com')->first();
$users = $model->where('status', 'active')->findAll();

// Multiple conditions (AND par défaut)
$users = $model
    ->where('status', 'active')
    ->where('role', 'admin')
    ->findAll();

// OR clause
$users = $model
    ->where('status', 'active')
    ->orWhere('status', 'pending')
    ->findAll();

// IN clause
$users = $model->whereIn('id', [1, 2, 3])->findAll();

// LIKE (recherche)
$users = $model->like('name', 'john')->findAll();     // john%
$users = $model->like('name', 'john', 'both')->findAll();   // %john%
$users = $model->like('name', 'john', 'after')->findAll();  // %john

// ORDER BY
$users = $model->orderBy('created_at', 'DESC')->findAll();
$users = $model->orderBy('name')->findAll();  // ASC par défaut

// LIMIT & OFFSET (pagination)
$users = $model->limit(10)->offset(20)->findAll();  // Page 3

// COUNT
$count = $model->countAll();  // COUNT(*)
$count = $model->where('is_active', true)->countAllResults();

// ========== INSERT ==========

$data = ['name' => 'John', 'email' => 'john@example.com'];

// Insert simple
$model->insert($data);
$insertId = $model->getInsertID();

// Insert avec validation automatique (appelle beforeInsert, etc.)
$model->save($data);  // save() = insert() + validation

// Batch insert
$data = [
    ['name' => 'John'],
    ['name' => 'Jane'],
];
$model->insertBatch($data);

// ========== UPDATE ==========

$model->update(1, ['name' => 'Jane']);  // Update par ID

// Update avec condition
$model->where('email', 'old@example.com')
      ->set(['email' => 'new@example.com'])
      ->update();

// Update batch
$data = [
    ['id' => 1, 'name' => 'John'],
    ['id' => 2, 'name' => 'Jane'],
];
$model->updateBatch($data);

// ========== DELETE ==========

$model->delete(1);  // Delete par ID

$model->where('status', 'deleted')
      ->delete();  // Delete avec condition
```

### Query Builder (sans Model)

Utiliser Query Builder directement quand tu n'as pas de Model :

```php
$db = \Config\Database::connect();

// ========== SELECT ==========

$result = $db->table('users')
    ->select('id, name, email')
    ->where('is_active', true)
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->get()
    ->getResult();  // stdObject objects
    // ou ->getResultArray() pour arrays associatifs

// ========== JOIN ==========

$result = $db->table('users u')
    ->select('u.name, p.title, COUNT(c.id) as comment_count')
    ->join('posts p', 'p.user_id = u.id', 'LEFT')
    ->join('comments c', 'c.post_id = p.id', 'LEFT')
    ->groupBy('p.id')
    ->having('comment_count', '>', 5)
    ->get()
    ->getResult();

// Types de JOIN : INNER, LEFT, RIGHT, OUTER, FULL OUTER

// ========== GROUP BY & HAVING ==========

$result = $db->table('posts')
    ->select('user_id, COUNT(*) as post_count')
    ->groupBy('user_id')
    ->having('post_count', '>', 5)
    ->get()
    ->getResult();

// ========== RAW QUERIES ==========

// Quand Query Builder ne suffit pas
$result = $db->query(
    'SELECT * FROM users WHERE email = ? AND role = ?',
    ['john@example.com', 'admin']
)->getResult();

// Named placeholders
$result = $db->query(
    'SELECT * FROM users WHERE email = :email: AND role = :role:',
    ['email' => 'john@example.com', 'role' => 'admin']
)->getResult();
```

### Explications Importantes

**`find()` vs `where()->first()`** :
```php
$user = $model->find(1);  // Cherche dans clé primaire uniquement

// Équivalent à :
$user = $model->where('id', 1)->first();  // WHERE id = 1

// Différent de :
$user = $model->where('email', 'john@example.com')->first();  // WHERE email = ?
```

**`save()` vs `insert()`** :
```php
// insert() : exécute directement INSERT
$model->insert($data);

// save() : vérifie si clé primaire existe
// - Si oui → UPDATE
// - Si non → INSERT
// Exécute aussi validation, callbacks beforeInsert/beforeUpdate
$model->save($data);  // Recommandé, plus flexible
```

**Mass Assignment** :
```php
// Seulement les champs dans $allowedFields peuvent être assignés
$model->insert($request->getPost());  // Les autres champs sont ignorés

// Protected : pas de risque d'injection de champs indésirables
// Ex : quelqu'un envoie 'is_admin' = true, mais n'est pas dans allowedFields → ignoré
```

---

## 4. Service Layer

### Pourquoi une Service Layer?

Controllers devrait faire **peu de choses** :
- Récupérer données requête
- Appeler Service
- Retourner réponse

Services contiennent la **logique métier** :
- Validation logique
- Calculs
- Interactions avec plusieurs Models
- Transactions BD
- Appels API externes
- Logging événements

**Bénéfice** : logique réutilisable, testable, indépendante du transport HTTP.

### Exemple Service Complet

```php
<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\LogModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UserService
{
    private UserModel $userModel;
    private LogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new LogModel();
    }

    /**
     * Crée nouvel utilisateur avec logique métier
     * 
     * @param array $data ['name', 'email', 'password']
     * @return int ID utilisateur créé
     * @throws \InvalidArgumentException
     */
    public function createUser(array $data): int
    {
        // Validation métier (complémentaire à validation model)
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Name, email, and password required');
        }

        // Transformation données
        $data['name'] = trim($data['name']);
        $data['email'] = strtolower($data['email']);

        // Vérifier email unique (logique métier, pas juste BD)
        if ($this->userModel->findByEmail($data['email'])) {
            throw new \InvalidArgumentException('Email already registered');
        }

        // Transaction BD (tout or nothing)
        $this->userModel->transBegin();
        
        try {
            // Enregistrer utilisateur
            $id = $this->userModel->insert($data);
            
            // Créer log événement
            $this->logModel->insert([
                'user_id' => $id,
                'event' => 'user.created',
                'data' => json_encode($data),
            ]);

            // Autres logiques métier : envoyer email, appels API, etc.
            // $this->sendWelcomeEmail($data['email']);

            $this->userModel->transCommit();
            
            return $id;
        } catch (\Exception $e) {
            $this->userModel->transRollback();
            throw $e;
        }
    }

    /**
     * Récupère utilisateur avec vérification permissions
     */
    public function getUser(int $id, int $requesterId): ?array
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        // Logique métier : masquer champs sensibles
        if ($requesterId !== $id && !$this->isAdmin($requesterId)) {
            unset($user['email']);
            unset($user['password']);
        }

        return $user;
    }

    /**
     * Liste utilisateurs avec filtrage & pagination
     * 
     * @param int $page Numéro page (1-indexed)
     * @param int $perPage Résultats par page
     * @param array $filters ['search', 'status', 'role']
     * @return array ['data' => [...], 'total' => X, 'page' => Y, 'pages' => Z]
     */
    public function listUsers(
        int $page = 1,
        int $perPage = 20,
        array $filters = []
    ): array {
        $query = $this->userModel;

        // Appliquer filtres
        if (!empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        if (!empty($filters['role'])) {
            $query = $query->where('role', $filters['role']);
        }

        // Recherche texte (multiple colonnes)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query = $query->groupStart()  // (
                          ->like('name', $search)
                          ->orLike('email', $search)
                    ->groupEnd();  // )
        }

        // Pagination
        $total = $query->countAllResults(false);  // false = pas reset query
        $offset = ($page - 1) * $perPage;
        
        $users = $query
            ->orderBy('created_at', 'DESC')
            ->limit($perPage)
            ->offset($offset)
            ->findAll();

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => ceil($total / $perPage),
        ];
    }

    /**
     * Update utilisateur
     */
    public function updateUser(int $id, array $data): bool
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        // Validation métier
        if (isset($data['email']) && $data['email'] !== $user['email']) {
            if ($this->userModel->findByEmail($data['email'])) {
                throw new \InvalidArgumentException('Email already in use');
            }
        }

        return $this->userModel->update($id, $data);
    }

    /**
     * Delete utilisateur
     */
    public function deleteUser(int $id): bool
    {
        // Logique métier : soft delete, cleanup data, etc.
        return $this->userModel->delete($id);
    }

    /**
     * Helper : vérifier si utilisateur est admin
     */
    private function isAdmin(int $userId): bool
    {
        $user = $this->userModel->find($userId);
        return $user && $user['role'] === 'admin';
    }
}
?>
```

### Controller utilisant Service

```php
<?php

namespace App\Controllers;

use App\Services\UserService;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Liste utilisateurs
     * GET /users?page=1&search=john&status=active
     */
    public function index(): ResponseInterface
    {
        $page = (int) ($this->request->getVar('page') ?? 1);
        $filters = [
            'search' => $this->request->getVar('q'),
            'status' => $this->request->getVar('status'),
            'role' => $this->request->getVar('role'),
        ];

        $data = $this->userService->listUsers($page, 20, $filters);
        return $this->response->setJSON($data);
    }

    /**
     * Voir un utilisateur
     * GET /users/1
     */
    public function show($id = null): ResponseInterface
    {
        try {
            $userId = (int) auth()->user()->id;  // Utilisateur connecté
            $user = $this->userService->getUser($id, $userId);
            return $this->response->setJSON($user);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'User not found'
            ]);
        }
    }

    /**
     * Créer utilisateur
     * POST /users
     * Body: {"name": "...", "email": "...", "password": "..."}
     */
    public function store(): ResponseInterface
    {
        $data = $this->request->getJSON(true);

        try {
            $id = $this->userService->createUser($data);
            return $this->response->setStatusCode(201)->setJSON([
                'id' => $id,
                'message' => 'User created successfully'
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            log_msg('error', $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Server error'
            ]);
        }
    }

    /**
     * Mettre à jour utilisateur
     * PUT /users/1
     */
    public function update($id = null): ResponseInterface
    {
        $data = $this->request->getJSON(true);

        try {
            $this->userService->updateUser($id, $data);
            return $this->response->setJSON(['success' => true]);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'User not found'
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Supprimer utilisateur
     * DELETE /users/1
     */
    public function delete($id = null): ResponseInterface
    {
        try {
            $this->userService->deleteUser($id);
            return $this->response->setStatusCode(204);
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'User not found'
            ]);
        }
    }
}
?>
```

### Injection Dépendances (Services.php)

Enregistrer services dans `app/Config/Services.php` pour réutilisation simple :

```php
<?php

namespace Config;

use App\Services\UserService;
use App\Services\EmailService;

class Services extends \CodeIgniter\Config\BaseService
{
    /**
     * Récupère singleton UserService
     * 
     * @param bool $getShared Si true = même instance partout
     */
    public static function userService(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('userService');
        }

        return new UserService();
    }

    /**
     * EmailService
     */
    public static function emailService(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('emailService');
        }

        return new EmailService();
    }
}
?>
```

**Usage** :
```php
// Dans un contrôleur
$userService = service('userService');  // Récupère singleton
$users = $userService->listUsers();

// Ou injecter dans constructeur
class UserController extends BaseController
{
    public function __construct(private UserService $userService) {}
    
    public function index()
    {
        $users = $this->userService->listUsers();
    }
}
```

---

## 5. Validation & Gestion d'Erreurs

### Validation dans Model

```php
class UserModel extends Model
{
    protected array $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]|alpha_space',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]|regex_match[/(?=.*[A-Z])(?=.*[0-9])/]',
        'age' => 'integer|greater_than[17]|less_than[120]',
        'phone' => 'regex_match[/^\+?[1-9]\d{1,14}$/]',
        'website' => 'valid_url',
        'terms' => 'required',  // Checkbox doit être présent
    ];

    protected array $validationMessages = [
        'email' => [
            'valid_email' => 'Veuillez entrer une adresse email valide.',
            'is_unique' => 'Cette adresse email existe déjà.',
            'required' => 'Email est requis.',
        ],
        'password' => [
            'regex_match' => 'Le mot de passe doit contenir au moins une majuscule et un chiffre.',
        ],
    ];
}
```

Quand utiliser :
- Validation **auto** avec `$model->save()` ou `$model->insert()`
- Validation appelée automatiquement si règles définies

### Validation dans Controller

```php
public function store()
{
    $rules = [
        'name' => 'required|min_length[3]',
        'email' => 'required|valid_email',
        'age' => 'required|numeric|greater_than[17]',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setStatusCode(422)->setJSON([
            'errors' => $this->validator->getErrors()
        ]);
    }

    // Les données sont validées, continuer
    $data = $this->request->getPost(['name', 'email', 'age']);
}
```

### Règles de Validation Courantes

```php
// ========== PRÉSENCE ==========
required              // Champ obligatoire
required_if[field,value]   // Obligatoire si autre champ = valeur
required_without[field]    // Obligatoire si autre champ absent

// ========== LONGUEUR ==========
min_length[5]         // Minimum 5 caractères
max_length[255]       // Maximum 255 caractères
exact_length[10]      // Exactement 10 caractères

// ========== TYPES ==========
integer               // Nombre entier
decimal[,2]           // Nombre décimal (2 décimales)
numeric               // Nombre (int ou decimal)
alpha                 // Lettres uniquement
alphanumeric          // Lettres + chiffres
alpha_space           // Lettres + espaces
alpha_dash            // Lettres + tirets + underscores

// ========== FORMAT ==========
valid_email           // Format email valide
valid_url             // Format URL valide
valid_ip              // IPv4 valide
valid_ipv4
valid_ipv6
regex_match[/regex/]  // Correspond à regex

// ========== COMPARAISON ==========
matches[field]        // Égal à autre champ
differs[field]        // Différent de autre champ
greater_than[5]       // > 5
less_than[100]        // < 100
greater_than_equal_to[10]
less_than_equal_to[50]

// ========== LISTE ==========
in_list[a,b,c]        // Valeur dans liste
not_in_list[x,y]      // Valeur NON dans liste

// ========== UNIQUE/EXISTS ==========
is_unique[users.email]          // Email unique dans table
is_unique[users.email,id,1]     // Unique SAUF pour id=1 (update)
is_not_unique[users.email]      // Doit exister dans table
```

### Exception Handling

```php
<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductController extends BaseController
{
    public function show($id)
    {
        try {
            $product = service('productService')->getProduct($id);
            return $this->response->setJSON($product);
        } catch (PageNotFoundException $e) {
            // 404 - Ressource inexistante
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['error' => 'Product not found']);
        } catch (\InvalidArgumentException $e) {
            // 400 - Données invalides
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => $e->getMessage()]);
        } catch (DatabaseException $e) {
            // 500 - Erreur serveur
            log_msg('error', 'Database error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Server error']);
        } catch (\Exception $e) {
            // Catch-all
            log_msg('error', $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'An error occurred']);
        }
    }
}
?>
```

### Levés Exceptions Personnalisées

```php
// Dans Service
if (!isset($product)) {
    throw new \CodeIgniter\Exceptions\PageNotFoundException(
        'Product with ID ' . $id . ' not found'
    );
}

if (empty($data['name'])) {
    throw new \InvalidArgumentException('Name is required');
}

// Dans Controller (catch + format response)
try {
    $data = service('productService')->createProduct($data);
} catch (\InvalidArgumentException $e) {
    return $this->response->setStatusCode(400)->setJSON([
        'error' => $e->getMessage()
    ]);
} catch (PageNotFoundException $e) {
    return $this->response->setStatusCode(404)->setJSON([
        'error' => $e->getMessage()
    ]);
}
```

---

## 6. Migrations & Seeds

### Pourquoi Migrations?

- **Versionnage schéma** : historique complet des changements BD
- **Réproductibilité** : appliquer mêmes changements sur dev, staging, prod
- **Rollback** : annuler derniers changements si problème
- **Collaboration** : Git tracks migrations, pas SQL dump

### Créer Migration

Terminal :
```bash
php spark migrate:make create_users_table
php spark migrate:make add_email_to_users
```

Fichier créé : `app/Database/Migrations/2024-01-10-000001_CreateUsersTable.php`

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // Définir colonnes
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => false,
            ],
            'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'user', 'moderator'],
                'default' => 'user',
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Clés primaire / unique
        $this->forge->addKey('id', true);          // PRIMARY KEY
        $this->forge->addUniqueKey('email');       // UNIQUE INDEX
        $this->forge->addKey('created_at');        // INDEX (performance)

        // Créer table
        $this->forge->createTable('users');
    }

    public function down()
    {
        // Annuler migration (rollback)
        $this->forge->dropTable('users');
    }
}
?>
```

### Modifier Table (ALTER)

```php
<?php

class AddVerificationCodeToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['verification_code', 'verified_at']);
    }
}
?>
```

### Seeding (Données Initiales)

`app/Database/Seeds/UserSeeder.php` :
```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 100; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => $i === 1 ? 'admin' : 'user',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Insérer par batch (plus rapide)
        $this->db->table('users')->insertBatch($data);
    }
}
?>
```

Main seeder `app/Database/Seeds/DatabaseSeeder.php` :
```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Exécuter seeders dans l'ordre
        $this->call('UserSeeder');
        $this->call('PostSeeder');
        $this->call('CommentSeeder');
    }
}
?>
```

### Commandes Migrations

```bash
# Appliquer toutes migrations non-appliquées
php spark migrate

# Appliquer jusqu'à une migration spécifique
php spark migrate --target 2024-01-15-000002

# Afficher statut migrations
php spark migrate:status

# Rollback dernière migration
php spark migrate:rollback

# Rollback TOUTES les migrations
php spark migrate:rollback --all

# Refresh = rollback toutes + migrate à nouveau
php spark migrate:refresh

# Refresh + seed
php spark migrate:refresh --seed

# Exécuter seeder spécifique
php spark db:seed UserSeeder

# Exécuter main seeder
php spark db:seed DatabaseSeeder
```

---

## 7. Configuration

### .env et Variables Environnement

Fichier `.env` (local, **jamais** committer) :
```env
# App
CI_ENVIRONMENT = development
APP_TIMEZONE = UTC
APP_BASEURL = http://localhost:8080/

# Database
DATABASE_DEFAULT_DRIVER = MySQLi
DATABASE_DEFAULT_HOSTNAME = localhost
DATABASE_DEFAULT_USERNAME = root
DATABASE_DEFAULT_PASSWORD = secret123
DATABASE_DEFAULT_DATABASE = myapp_db
DATABASE_DEFAULT_PORT = 3306

# Custom
ADMIN_EMAIL = admin@example.com
JWT_SECRET = your-secret-key-change-in-production
SMTP_HOST = smtp.gmail.com
SMTP_USER = your-email@gmail.com
SMTP_PASS = app-password
```

Utiliser variables :
```php
// Dans n'importe quel fichier
$env = getenv('CI_ENVIRONMENT');     // "development"
$dbName = getenv('DATABASE_DEFAULT_DATABASE');  // "myapp_db"
$secret = getenv('JWT_SECRET');
```

### app/Config/App.php

Fichier : `app/Config/App.php`

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    // URL de base de l'app
    public string $baseURL = 'http://localhost:8080/';

    // Index fichier (laisse vide si .htaccess actif)
    public string $indexPage = '';

    // Protocol pour générer URLs
    public string $uriProtocol = 'REQUEST_URI';

    // Controller & méthode par défaut
    public string $defaultController = 'Home';
    public string $defaultMethod = 'index';

    // Timezone par défaut
    public string $appTimezone = 'UTC';

    // Suffix pour views (vues.php)
    public string $viewSuffix = '.php';

    // Enable security headers
    public bool $enableSecureHeaders = true;

    // CSRF enabled
    public bool $enableCSRFProtection = true;
}
?>
```

### app/Config/Database.php

```php
<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public array $default = [
        'DSN' => '',  // Vide = utiliser paramètres séparés
        'hostname' => getenv('DATABASE_DEFAULT_HOSTNAME'),
        'username' => getenv('DATABASE_DEFAULT_USERNAME'),
        'password' => getenv('DATABASE_DEFAULT_PASSWORD'),
        'database' => getenv('DATABASE_DEFAULT_DATABASE'),
        'DBDriver' => getenv('DATABASE_DEFAULT_DRIVER') ?: 'MySQLi',
        'DBPrefix' => '',  // Préfixe tables (ex: "app_users")
        'pConnect' => false,  // Persistent connection
        'DBDebug' => (getenv('CI_ENVIRONMENT') === 'development'),
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'strictOn' => true,  // Strict mode MySQL
        'port' => (int) getenv('DATABASE_DEFAULT_PORT') ?: 3306,
    ];

    // Connexion secondaire (exemple)
    public array $secondary = [
        'DSN' => '',
        'hostname' => 'secondary-db.example.com',
        'username' => 'sec_user',
        'password' => 'sec_password',
        'database' => 'secondary_db',
        'DBDriver' => 'MySQLi',
    ];
}
?>
```

Utiliser connexion secondaire :
```php
$db = \Config\Database::connect('secondary');
$result = $db->table('users')->get()->getResult();
```

---

## 8. Sécurité

### Protection CSRF (Cross-Site Request Forgery)

Enregistrer filter : `app/Config/Filters.php`
```php
public array $methods = [
    'post' => ['csrf'],
    'put' => ['csrf'],
    'patch' => ['csrf'],
    'delete' => ['csrf'],
];
```

Ou route-level :
```php
$routes->post('/users', 'UserController::store', ['filter' => 'csrf']);
```

Formulaire HTML :
```html
<form method="POST" action="/users">
    <?= csrf_field() ?>
    <input type="text" name="name" />
    <button type="submit">Créer</button>
</form>
```

API (JavaScript) :
```javascript
fetch('/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('[name="csrf_token"]').value
    },
    body: JSON.stringify({ name: 'John' })
});
```

### Protection XSS (Cross-Site Scripting)

**Twig** (auto-échappe par défaut) :
```twig
{# Échappe HTML #}
{{ user.name }}

{# Affiche HTML brut (attention!) #}
{{ user.bio | raw }}
```

**PHP** :
```php
<?php
// Échapper pour HTML
echo esc($data);  // Par défaut = HTML

// Échapper pour JavaScript
echo esc($data, 'js');

// Échapper pour CSS
echo esc($data, 'css');

// Échapper pour attributs HTML
echo esc($data, 'attr');

// Exemple
<script>
    var name = "<?= esc($username, 'js') ?>";
</script>
```

### SQL Injection Protection

**❌ JAMAIS** :
```php
$query = "SELECT * FROM users WHERE email = '" . $_POST['email'] . "'";
$result = $db->query($query);
```

**✅ TOUJOURS** - Placeholders :
```php
// Positional placeholders
$user = $db->query(
    'SELECT * FROM users WHERE email = ? AND role = ?',
    [$_POST['email'], 'admin']
)->getRow();

// Named placeholders
$user = $db->query(
    'SELECT * FROM users WHERE email = :email: AND role = :role:',
    ['email' => $_POST['email'], 'role' => 'admin']
)->getRow();
```

**✅ MEILLEUR** - Query Builder (échappe automatiquement) :
```php
$user = $db->table('users')
    ->where('email', $_POST['email'])
    ->where('role', 'admin')
    ->get()
    ->getRow();
```

### Authentification & JWT

Créer filter : `app/Filters/AuthFilter.php`
```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Récupérer token du header Authorization
        $auth = $request->getHeaderLine('Authorization');
        
        if (empty($auth)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Authorization header missing']);
        }

        try {
            // Format : "Bearer token_string"
            $token = str_replace('Bearer ', '', $auth);
            
            // Décoder JWT
            $key = new Key(getenv('JWT_SECRET'), 'HS256');
            $decoded = JWT::decode($token, $key);
            
            // Stocker utilisateur dans request pour utilisation plus tard
            $request->user = $decoded;
            
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid or expired token']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
?>
```

Enregistrer : `app/Config/Filters.php`
```php
public array $aliases = [
    'auth' => \App\Filters\AuthFilter::class,
];
```

Utiliser dans routes :
```php
$routes->group('api', ['filter' => 'auth'], static function($routes) {
    $routes->get('users', 'Api\UserController::index');
    $routes->post('users', 'Api\UserController::store');
});
```

Générer JWT (login) :
```php
<?php

namespace App\Services;

use Firebase\JWT\JWT;

class AuthService
{
    public function generateToken(array $userData): string
    {
        $issued = time();
        $expires = $issued + (60 * 60 * 24);  // 24 heures

        $payload = [
            'iat' => $issued,
            'exp' => $expires,
            'id' => $userData['id'],
            'email' => $userData['email'],
            'role' => $userData['role'],
        ];

        return JWT::encode(
            $payload,
            getenv('JWT_SECRET'),
            'HS256'
        );
    }
}
?>
```

Login endpoint :
```php
public function login()
{
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $user = service('userService')->findByEmail($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return $this->response->setStatusCode(401)->setJSON([
            'error' => 'Invalid credentials'
        ]);
    }

    $token = service('authService')->generateToken($user);

    return $this->response->setJSON([
        'token' => $token,
        'user' => $user,
    ]);
}
```

### Password Hashing

```php
// Hasher password à l'insertion
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Vérifier password lors login
if (password_verify($inputPassword, $hashedPassword)) {
    // Correct
}

// Ou dans callback Model :
protected array $beforeInsert = ['hashPassword'];

public function hashPassword(array $data)
{
    if (isset($data['data']['password'])) {
        $data['data']['password'] = password_hash(
            $data['data']['password'],
            PASSWORD_BCRYPT
        );
    }
    return $data;
}
```

---

## 9. Patterns Avancés

### Repository Pattern

Abstraire logique accès données :

Interface : `app/Repositories/UserRepositoryInterface.php`
```php
<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function find($id);
    public function findAll($limit, $offset);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByEmail(string $email);
}
?>
```

Implémentation : `app/Repositories/UserRepository.php`
```php
<?php

namespace App\Repositories;

use App\Models\UserModel;

class UserRepository implements UserRepositoryInterface
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findAll($limit = 20, $offset = 0)
    {
        return $this->model
            ->limit($limit)
            ->offset($offset)
            ->findAll();
    }

    public function create(array $data)
    {
        return $this->model->insert($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
?>
```

Service utilisant repository :
```php
class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function getUserWithPagination($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        return $this->repo->findAll($perPage, $offset);
    }
}
```

**Bénéfice** : changer implémentation (Model → API distante) sans toucher Service.

### Events (Listeners)

Découpler logique : quand utilisateur créé → déclencher événement.

Déclencher event :
```php
// Dans Service
$id = $this->userModel->insert($data);
Events::trigger('user.created', ['user_id' => $id, 'email' => $data['email']]);
```

Listener : `app/Listeners/UserCreatedListener.php`
```php
<?php

namespace App\Listeners;

class UserCreatedListener
{
    public function handle(array $data)
    {
        // Envoyer email bienvenue
        service('email')->sendWelcomeEmail($data['email']);
        
        // Logger
        log_msg('info', 'User created: ' . $data['email']);
        
        // Créer notification
        // service('notification')->notify(...);
    }
}
?>
```

Enregistrer : `app/Config/Events.php`
```php
<?php

namespace Config;

use CodeIgniter\Events\Events;

Events::on('user.created', [\App\Listeners\UserCreatedListener::class, 'handle']);
Events::on('user.deleted', function(array $data) {
    log_msg('info', 'User deleted: ' . $data['user_id']);
});
?>
```

### Pagination Serveur-Side

```php
public function index()
{
    $perPage = 15;
    $page = (int) ($this->request->getVar('page') ?? 1);
    
    $postModel = new PostModel();
    $total = $postModel->countAllResults();
    
    $posts = $postModel
        ->orderBy('created_at', 'DESC')
        ->limit($perPage)
        ->offset(($page - 1) * $perPage)
        ->findAll();
    
    // Générer liens pagination
    $pager = service('pager');
    $pagerLinks = $pager->makeLinks($page, $perPage, $total);
    
    return view('posts/index', [
        'posts' => $posts,
        'pager' => $pagerLinks,
    ]);
}
```

Template :
```twig
{% for post in posts %}
    <h3>{{ post.title }}</h3>
    <p>{{ post.content }}</p>
{% endfor %}

{{ pager_links }}
```

### Soft Deletes

Au lieu de supprimer, marquer comme supprimé :

Migration :
```php
$this->forge->addColumn('posts', [
    'deleted_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ],
]);
```

Model :
```php
class PostModel extends Model
{
    protected bool $useSoftDeletes = true;
    protected string $deletedField = 'deleted_at';
    
    // Récupère records NON supprimés (automatique)
    // $model->findAll() exclut deleted_at IS NOT NULL
}
```

Opérations :
```php
// Soft delete
$model->delete(1);  // Set deleted_at = NOW()

// Voir tous incluant supprimés
$posts = $model->withDeleted()->findAll();

// Voir SEULEMENT supprimés
$posts = $model->onlyDeleted()->findAll();

// Restaurer
$model->update(1, ['deleted_at' => null]);

// Force delete (vrai suppression)
$model->forceDelete(1);
```

### Timestamps Automatiques

Model :
```php
class UserModel extends Model
{
    protected bool $useTimestamps = true;
    protected string $createdField = 'created_at';
    protected string $updatedField = 'updated_at';
    protected string $dateFormat = 'datetime';
}
```

Automatiquement :
```php
$model->insert(['name' => 'John']);
// created_at = NOW(), updated_at = NOW()

$model->update(1, ['name' => 'Jane']);
// updated_at = NOW()  (created_at ne change pas)
```

---

## 10. Debugging & Optimisation

### Logging

```php
$logger = service('logger');

$logger->emergency('Emergency level');
$logger->alert('Alert level');
$logger->critical('Critical level');
$logger->error('Error level');
$logger->warning('Warning level');
$logger->notice('Notice level');
$logger->info('Info level');
$logger->debug('Debug level');

// Ou shortcut
log_msg('error', 'Database connection failed');
log_msg('info', 'User registered: ' . $email);
```

Logs stockés dans : `writable/logs/`

Configuration : `app/Config/Logger.php`

### Query Debugging

Mode debug activé dans `.env` :
```env
CI_ENVIRONMENT = development
```

Afficher dernière requête :
```php
$db = \Config\Database::connect();
$users = $db->table('users')->get()->getResult();
echo $db->getLastQuery();  // Affiche : SELECT * FROM users
```

Afficher toutes queries (Debug Bar) :
- Activer en développement uniquement
- Visible en bas de page (toolbar)
- Montre queries, timing, memory, etc.

### Query Optimization

**N+1 Problem** ❌ :
```php
$posts = $model->findAll();
foreach ($posts as $post) {
    $comments = $commentModel->where('post_id', $post['id'])->findAll();
    // X requêtes! (N+1)
}
```

**Eager Loading** ✅ :
```php
// Join + GROUP BY
$posts = $db->table('posts p')
    ->select('p.*, COUNT(c.id) as comment_count')
    ->join('comments c', 'c.post_id = p.id', 'LEFT')
    ->groupBy('p.id')
    ->get()
    ->getResult();
```

**Batch Query** ✅ :
```php
$postIds = array_column($posts, 'id');
$comments = $commentModel->whereIn('post_id', $postIds)->findAll();

// Grouper par post_id
$commentsByPost = [];
foreach ($comments as $comment) {
    $commentsByPost[$comment['post_id']][] = $comment;
}
```

### Database Indexes

Migration avec indexes :
```php
$this->forge->addKey('id', true);                    // PRIMARY KEY
$this->forge->addKey('email');                       // INDEX
$this->forge->addUniqueKey('username');              // UNIQUE INDEX
$this->forge->addKey(['user_id', 'created_at']);    // Composite INDEX
```

**Quand ajouter index** :
- Colonnes dans WHERE clause
- Colonnes dans JOIN conditions
- Colonnes dans ORDER BY
- Colonnes dans LIKE (si début pattern)

### Caching

Mettre en cache résultats coûteux :

```php
$cache = service('cache');

// Stocker en cache (3600 secondes = 1 heure)
$popularPosts = $model->where('views', '>', 1000)->findAll();
$cache->save('popular_posts', $popularPosts, 3600);

// Récupérer du cache
$posts = $cache->get('popular_posts');

// Supprimer du cache
$cache->delete('popular_posts');

// Get or cache (remember pattern)
$posts = $cache->remember('popular_posts', 3600, function() {
    return service('postService')->getPopularPosts();
});
```

Configuration cache : `app/Config/Cache.php`

Types de cache (configuration) :
- `file` : stockage fichiers (défaut)
- `redis` : stockage Redis (prod recommandé)
- `memcached` : stockage Memcached

### Performance Tips

1. **Indexes** : ajouter sur colonnes WHERE/JOIN/ORDER BY
2. **Eager Loading** : éviter N+1 queries
3. **Pagination** : limiter résultats par page
4. **Caching** : cache queries coûteuses
5. **Batch Operations** : `insertBatch()`, `updateBatch()`
6. **Select Colonnes** : `select('id, name')` au lieu de `*`
7. **Lazy Loading** : charger données au besoin, pas à l'avance
8. **Query Builder** : filter au niveau BD, pas application

Profiler :
```bash
# Enable debug bar (development)
CI_ENVIRONMENT = development

# Voir : timing, queries, memory usage, etc.
```

---

## Résumé : Bonnes Pratiques

✅ **Séparation des responsabilités** :
- Controller : reçoit requête, appelle Service, retourne réponse
- Service : logique métier, validation, transactions
- Model : accès données, validation simple

✅ **Validation** :
- Model : `$validationRules` pour règles métier
- Controller : vérifier response validation dans action

✅ **Gestion Erreurs** :
- Lever exceptions spécifiques (PageNotFoundException, InvalidArgumentException)
- Catch dans controller, retourner response appropriée

✅ **Database** :
- Migrations pour versionnage schéma
- Seeds pour données initiales
- Parameterized queries pour éviter SQL injection

✅ **Sécurité** :
- CSRF protection sur POST/PUT/DELETE
- XSS escaping (esc() ou Twig)
- Password hashing (PASSWORD_BCRYPT)
- JWT pour API authentification

✅ **Performance** :
- Indexes sur colonnes WHERE/JOIN/ORDER BY
- Eager loading (JOIN, withDeleted, etc.)
- Caching résultats coûteux
- Pagination (pas findAll() sans limit)

✅ **Code Quality** :
- Repository pattern pour abstraction
- Events pour découpler logique
- Service layer pour réutilisabilité
- Logging pour debugging

---

## Ressources

- [Documentation CI4 Officielle](https://codeigniter.com/user_guide/index.html)
- [GitHub CodeIgniter 4](https://github.com/codeigniter4/CodeIgniter4)
- [Spark CLI Commands](https://codeigniter.com/user_guide/cli/spark_commands.html)

**Bon développement! 🚀**
