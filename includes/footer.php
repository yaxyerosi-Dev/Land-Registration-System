        </main>
        <footer class="app-footer text-center py-3">
            <small>&copy; <?= date('Y') ?> <?= e(getSettings()['system_name'] ?? APP_NAME) ?>. All rights reserved.</small>
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
