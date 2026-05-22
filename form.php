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

            <?php if (!empty($messages)): ?>
                <div style="margin: 20px 0; padding: 10px; background: #d4edda; border-radius: 5px;">
                    <?php foreach ($messages as $msg): ?>
                        <div><?= $msg ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_messages)): ?>
                <div style="margin: 20px 0; padding: 10px; background: #f8d7da; border-radius: 5px; color: #721c24;">
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
    
    <label for="FIO">ФИО:</label>

    <input type="text" id="FIO" name="FIO" value="<?= htmlspecialchars($values['FIO'], ENT_QUOTES, 'UTF-8') ?>" 
           style="<?= !empty($errors['FIO']) ? 'border: 2px solid red;' : '' ?>">

    <?php if (!empty($errors['FIO'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['FIO'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label for="telep">Телефон:</label>

    <input type="text" id="telep" name="telep" value="<?= htmlspecialchars($values['telep'], ENT_QUOTES, 'UTF-8') ?>" 
           style="<?= !empty($errors['telep']) ? 'border: 2px solid red;' : '' ?>">

    <?php if (!empty($errors['telep'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['telep'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label for="mail">Email:</label>

    <input type="email" id="mail" name="mail" value="<?= htmlspecialchars($values['mail'], ENT_QUOTES, 'UTF-8') ?>" 
           style="<?= !empty($errors['mail']) ? 'border: 2px solid red;' : '' ?>">

    <?php if (!empty($errors['mail'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['mail'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label for="date">Дата рождения:</label>

    <input type="date" id="date" name="date" value="<?= htmlspecialchars($values['date'], ENT_QUOTES, 'UTF-8') ?>" 
           style="<?= !empty($errors['date']) ? 'border: 2px solid red;' : '' ?>">

    <?php if (!empty($errors['date'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['date'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label>Пол:</label>

    <input type="radio" name="sex" value="Male" <?= $values['sex'] == 'Male' ? 'checked' : '' ?>> Male
    <input type="radio" name="sex" value="Female" <?= $values['sex'] == 'Female' ? 'checked' : '' ?>> Female

    <?php if (!empty($errors['sex'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['sex'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label for="language">Языки программирования:</label>

    <select id="language" name="language[]" multiple size="6" 
            style="<?= !empty($errors['language']) ? 'border: 2px solid red;' : '' ?>">
        <option value="PHP" <?= in_array('PHP', $values['language']) ? 'selected' : '' ?>>PHP</option>
        <option value="Python" <?= in_array('Python', $values['language']) ? 'selected' : '' ?>>Python</option>
        <option value="Java" <?= in_array('Java', $values['language']) ? 'selected' : '' ?>>Java</option>
        <option value="JavaScript" <?= in_array('JavaScript', $values['language']) ? 'selected' : '' ?>>JavaScript</option>
        <option value="C++" <?= in_array('C++', $values['language']) ? 'selected' : '' ?>>C++</option>
        <option value="Go" <?= in_array('Go', $values['language']) ? 'selected' : '' ?>>Go</option>
    </select>

    <?php if (!empty($errors['language'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['language'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    
    <label for="bio">Биография:</label>

    <textarea id="bio" name="bio" rows="5" cols="40" 
              style="<?= !empty($errors['bio']) ? 'border: 2px solid red;' : '' ?>"><?= htmlspecialchars($values['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>

    <?php if (!empty($errors['bio'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['bio'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <label>
        <input type="checkbox" name="agreement" <?= $values['agreement'] == 'on' ? 'checked' : '' ?>>
        Согласен с контрактом
    </label>

    <?php if (!empty($errors['agreement'])): ?>
        <span style="color: red;"><?= htmlspecialchars($error_messages['agreement'], ENT_QUOTES, 'UTF-8') ?></span>

    <?php endif; ?>
    
    <input type="submit" value="Сохранить">
            </form>

            <div style="text-align: center; margin-top: 30px;">
                <?php if (!empty($_SESSION['login'])): ?>
                    <span>✓ Вы вошли как <strong><?= htmlspecialchars($_SESSION['login'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                    <a href="login.php?logout=1" style="margin-left: 15px; color: #f44336;">Выйти</a>
                <?php else: ?>
                    <a href="login.php">🔐 Войти для изменения данных</a>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>
</html>
