# Git Access Guard

Repository ini dikonfigurasi agar operasi `commit` dan `push` hanya boleh dilakukan oleh akun `andhyabernaz`.

## Yang dipasang

- Local Git config untuk:
  - `user.name = andhyabernaz`
  - `user.email = andhyabernaz@gmail.com`
  - `core.hooksPath = .githooks`
  - `credential.https://github.com.username = andhyabernaz`
- Hook versioned:
  - `.githooks/pre-commit`
  - `.githooks/pre-push`
- Validator utama di `scripts/git-access-guard.php`

## Rule yang ditegakkan

### `pre-commit`

- Menolak commit bila `git config user.name` bukan `andhyabernaz`
- Menolak commit bila `git config user.email` bukan `andhyabernaz@gmail.com`

### `pre-push`

- Menjalankan semua validasi `pre-commit`
- Menolak push bila remote host bukan `github.com`
- Menolak push bila owner repository pada push URL bukan `andhyabernaz`
- Menolak push bila username credential yang terkonfigurasi bukan `andhyabernaz`
- Jika `gh` CLI tersedia, hook juga memvalidasi akun login `gh auth status`

## Setup

Jalankan:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup-git-access-guard.ps1
```

Script ini hanya mengubah konfigurasi Git lokal repository ini, jadi tidak menyentuh global config user lain.

Jika setup dijalankan di Linux/macOS, pastikan hook executable:

```bash
chmod +x .githooks/pre-commit .githooks/pre-push
```

## Testing

Test otomatis:

```powershell
php .\scripts\test-git-access-guard.php
```

Test ini membuat temporary repository, mengaktifkan hook yang versioned, lalu memverifikasi:

- user `andhyabernaz` bisa commit
- user lain ditolak saat commit
- credential `andhyabernaz` lolos validasi push
- credential lain ditolak saat push

## Manual verification

### Commit harus ditolak untuk user lain

```powershell
git config --local user.name other-user
git commit --allow-empty -m "should fail"
```

### Push harus ditolak untuk credential lain

```powershell
git config --local credential.https://github.com.username other-user
php .\scripts\git-access-guard.php pre-push origin "$(git remote get-url --push origin)"
```

## Catatan kolaborasi

- Workflow kolaboratif seperti `fetch`, `pull`, `checkout`, `merge`, dan review file tidak diblokir
- Pembatasan hanya aktif pada `commit` dan `push`
- Karena ini enforcement lokal berbasis hook, proteksi final tetap sebaiknya dipadukan dengan permission remote repository
