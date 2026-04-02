# File Module

Centralized file management with database tracking, namespace-based organization, and automatic versioning.

## Design

Every file in the system has a database record. Files are organized by namespace (a slug like `user-avatars`, `documents`). The namespace maps to a directory on disk. Files are versioned: uploading a file with the same name in the same namespace creates a new version rather than overwriting. Storage paths use UUIDs to prevent collisions.

Direct filesystem access (`Storage::`, `fopen`, `file_get_contents`, etc.) is banned outside `Infrastructure\Filesystem\` by PHPStan rules. All file operations go through the `FileStorage` contract. A dedicated `files` disk in `config/filesystems.php` is used -- swap to S3 by setting `FILES_DISK_DRIVER=s3` and configuring S3 credentials.

### Schema

- `files` — id, namespace, original_name, storage_path (unique), mime_type, size_in_bytes, version_number, uploaded_by FK, uploaded_at, version (optimistic locking), timestamps, soft deletes

### Storage Path Strategy

On disk: `{namespace}/{uuid}.{extension}`. Each upload gets a unique UUID-based path — no overwriting, no collisions. Version tracking is purely in the database (same `original_name` + `namespace`, incrementing `version_number`).

### FileUpload Value Object

Domain-level file input abstraction wrapping `\SplFileInfo` with metadata (`FileName`, `MimeType`, `int $sizeInBytes`). Controllers create it from Laravel's `UploadedFile` (which extends `SplFileInfo`). Infrastructure reads from it via `SplFileInfo::getPathname()`.

### Permissions

| Action | Permission | Who checks |
|--------|-----------|-----------|
| Upload file | `files.storage.upload` | Middleware via `#[RequiresPermission]` |
| Read file | `files.storage.read` | Middleware via `#[RequiresPermission]` |
| Delete file | `files.storage.delete` | Middleware via `#[RequiresPermission]` |

## Commands

| Command | Permission | Purpose |
|---------|-----------|---------|
| `StoreFileCommand` | `files.storage.upload` | Upload a file, compute version, persist record |
| `DeleteFileCommand` | `files.storage.delete` | Delete file from storage and database |

## Queries

| Query | Permission | Returns |
|-------|-----------|---------|
| `GetFileByIdQuery` | `files.storage.read` | `File` (throws `FileNotFoundException`) |
| `GetFileVersionsQuery` | `files.storage.read` | `list<File>` all versions ordered by version_number |
| `GetLatestFileVersionQuery` | `files.storage.read` | `File` latest version (throws `FileNotFoundException`) |

## Events

- `FileStored` — file uploaded and persisted
- `FileDeleted` — file removed (implements `EntityDeleted` for label cleanup)

## Contracts

- `FileRepository` — CRUD + version queries for file database records
- `FileStorage` — store/retrieve/delete/exists/url for actual file bytes. Infrastructure implements with Laravel's `Filesystem`.

## PHPStan Enforcement

- `NoDirectFilesystemAccessRule` — bans `Storage::` facade, `storage_path()`, and PHP file functions (`fopen`, `file_get_contents`, `unlink`, etc.) outside `Infrastructure\Filesystem\`
- `NoDirectFilesystemImportRule` — bans `Illuminate\Filesystem\*` and `Illuminate\Contracts\Filesystem\*` imports outside `Infrastructure\Filesystem\`
