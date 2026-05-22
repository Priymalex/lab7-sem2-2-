<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрационная форма</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="form-container">
            <fieldset>
                <legend>Регистрационная форма</legend>

                <?php if (!empty($messages)): ?>
                    <div class="success">
                        <?php foreach ($messages as $msg): ?>
                            <div><?= $msg ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_messages)): ?>
                    <div class="error-list">
                        <strong>Исправьте следующие ошибки:</strong>
                        <ul>
                            <?php foreach ($error_messages as $field => $msg): ?>
                                <li><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                    <div class="form-group">
                        <label for="FIO">ФИО:</label>
                        <input type="text" id="FIO" name="FIO" 
                               placeholder="Иванов Иван Иванович"
                               value="<?= htmlspecialchars($values['FIO'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               class="<?= !empty($errors['FIO']) ? 'error' : '' ?>">
                        <?php if (!empty($errors['FIO'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['FIO'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="telep">Телефон:</label>
                        <input type="text" id="telep" name="telep" 
                               placeholder="+7 (999) 123-45-67"
                               value="<?= htmlspecialchars($values['telep'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               class="<?= !empty($errors['telep']) ? 'error' : '' ?>">
                        <?php if (!empty($errors['telep'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['telep'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="mail">Email:</label>
                        <input type="email" id="mail" name="mail" 
                               placeholder="ivanov@example.com"
                               value="<?= htmlspecialchars($values['mail'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               class="<?= !empty($errors['mail']) ? 'error' : '' ?>">
                        <?php if (!empty($errors['mail'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['mail'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="date">Дата рождения:</label>
                        <input type="date" id="date" name="date" 
                               placeholder="1990-01-01"
                               value="<?= htmlspecialchars($values['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               class="<?= !empty($errors['date']) ? 'error' : '' ?>">
                        <?php if (!empty($errors['date'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Пол:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="sex" value="Male" <?= ($values['sex'] ?? '') == 'Male' ? 'checked' : '' ?>> Мужской</label>
                            <label><input type="radio" name="sex" value="Female" <?= ($values['sex'] ?? '') == 'Female' ? 'checked' : '' ?>> Женский</label>
                        </div>
                        <?php if (!empty($errors['sex'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['sex'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="language">Языки программирования:</label>
                        <select id="language" name="language[]" multiple size="5" 
                                class="<?= !empty($errors['language']) ? 'error' : '' ?>">
                            <option value="PHP" <?= in_array('PHP', $values['language'] ?? []) ? 'selected' : ?>>PHP</option>
                            <option value="Python" <?= in_array('Python', $values['language'] ?? []) ? 'selected' : ?>>Python</option>
                            <option value="Java" <?= in_array('Java', $values['language'] ?? []) ? 'selected' : ?>>Java</option>
                            <option value="JavaScript" <?= in_array('JavaScript', $values['language'] ?? []) ? 'selected' : ?>>JavaScript</option>
                            <option value="C++" <?= in_array('C++', $values['language'] ?? []) ? 'selected' : ?>>C++</option>
                            <option value="Go" <?= in_array('Go', $values['language'] ?? []) ? 'selected' : ?>>Go</option>
                        </select>
                        <small>Зажмите Ctrl (Cmd) для выбора нескольких языков</small>
                        <?php if (!empty($errors['language'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['language'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="bio">Биография:</label>
                        <textarea id="bio" name="bio" rows="5" 
                                  placeholder="Расскажите немного о себе..." 
                                  class="<?= !empty($errors['bio']) ? 'error' : '' ?>"><?= htmlspecialchars($values['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <small>Максимум 1000 символов</small>
                        <?php if (!empty($errors['bio'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="agreement" value="on" <?= ($values['agreement'] ?? '') == 'on' ? 'checked' : '' ?>>
                            Я согласен с условиями контракта
                        </label>
                        <?php if (!empty($errors['agreement'])): ?>
                            <span class="error-message"><?= htmlspecialchars($error_messages['agreement'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit">Сохранить</button>
                </form>

                <div class="auth-link">
                    <?php if (!empty($_SESSION['login'])): ?>
                        <span>✓ Вы вошли как <strong><?= htmlspecialchars($_SESSION['login'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                        <a href="login.php?logout=1">Выйти</a>
                    <?php else: ?>
                        <a href="login.php">🔐 Уже зарегистрированы? Войти</a>
                    <?php endif; ?>
                </div>
            </fieldset>
        </div>
    </main>
</body>
</html>
