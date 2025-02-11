# 🛠️ Documentation OpenRouterService

## 📑 Table des Matières
1. [Structure du Service](#structure)
2. [Configuration](#configuration)
3. [Fonctionnalités](#fonctionnalites)
4. [Gestion des Erreurs](#erreurs)
5. [Exemple d'Utilisation](#utilisation)

<a name="structure"></a>
## 🏗️ Structure du Service

```php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenRouterService
{
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';
    
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $httpClient
    ) {}
}
```

### 🔑 Dépendances Clés
- `ParameterBagInterface`: Gestion des paramètres de configuration
- `HttpClientInterface`: Client HTTP Symfony pour les requêtes API

<a name="configuration"></a>
## ⚙️ Configuration

### Variables d'Environnement
```env
# .env
OPENROUTER_API_KEY=votre_clé_api
```

### Paramètres du Service
```yaml
# config/services.yaml
parameters:
    OPENROUTER_API_KEY: '%env(OPENROUTER_API_KEY)%'
```

<a name="fonctionnalites"></a>
## 🎯 Fonctionnalités

### Méthode getResponse
```php
public function getResponse(
    string $chat, 
    string $model = 'microsoft/phi-3-medium-128k-instruct:free'
): array
```

#### Paramètres
- `$chat`: Question/prompt de l'utilisateur
- `$model`: Modèle d'IA (par défaut: microsoft/phi-3) qui est gratuit

#### Configuration de la Requête
```php
$body = [
    'model' => $model,
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Tu dois répondre de maniere concise et en francais'.$chat
        ]
    ],
    'temperature' => 0.7 
];
```

#### En-têtes HTTP
```php
'headers' => [
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
    'HTTP-Referer' => 'http://localhost',
    'X-Title' => 'Mon Application'
]
```

<a name="erreurs"></a>
## 🚨 Gestion des Erreurs

```php
try {
    // ... Code de requête ...
} catch (\Exception $e) {
    throw new \RuntimeException('Erreur API OpenRouter : ' . $e->getMessage());
}
```

### Validation de la Réponse
```php
if (!isset($data['choices'][0]['message']['content'])) {
    throw new \RuntimeException('Structure de réponse inattendue : ' . json_encode($data));
}
```

<a name="utilisation"></a>
## 💻 Exemple d'Utilisation

### Dans un Controller
```php
class HomeController extends AbstractController
{
    public function index(Request $request, OpenRouterService $openRouter)
    {
        try {
            $response = $openRouter->getResponse("Quelle est la capitale de la France?");
            
            // Format de la réponse
            $content = $response['choices'][0]['message']['content'];
            $usage = $response['usage']; // Informations sur l'utilisation
            
        } catch (\RuntimeException $e) {
            // Gestion des erreurs
        }
    }
}
```

## 📊 Format des Réponses

### Succès
```json
{
    "choices": [
        {
            "message": {
                "content": "Paris est la capitale de la France.",
                "role": "assistant"
            }
        }
    ],
    "usage": {
        "prompt_tokens": 20,
        "completion_tokens": 10,
        "total_tokens": 30
    }
}
```

### Erreur
```php
throw new \RuntimeException('Erreur API OpenRouter : Message d\'erreur spécifique');
```

---

📫 **Support et Ressources**
- [Documentation API OpenRouter](https://openrouter.ai/docs)
- [Modèle Microsoft Phi-3](https://openrouter.ai/models)
