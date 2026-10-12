# Đại Phú Vinh – WordPress theme

- `dpv-theme/` – theme cha (chuẩn SEO, tương thích WooCommerce)
- `dpv-child/` – theme con (sửa riêng ở đây nếu có thể)
- `preview/` – công cụ xem trước trên máy (WordPress Playground), KHÔNG triển khai lên host

## Triển khai (cPanel › Git™ Version Control)
1. Tạo/clone repo này vào `~/repositories/daiphuvinh-theme` (ngoài `public_html`).
2. Mỗi lần có commit mới: **Update from Remote** rồi **Deploy HEAD Commit**.
3. `.cpanel.yml` sẽ chép `dpv-theme` và `dpv-child` vào `wp-content/themes`.

Quay lại bản cũ: `git revert <commit>` rồi triển khai lại.
