<div class="container-fluid my-4">
    <section class="modx-log-404">
        <div class="modx-log-404-heading d-flex justify-content-between align-items-center">
            <div>
                <h1><?php print $this->modx->lexicon('log404.cmp.title') ?></h1>
                <p><?php print $this->modx->lexicon('log404.cmp.desc') ?></p>
            </div>
            <div>
                <a class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" href="<?php print $this->page('download') ?>"><?php print $this->modx->lexicon('log404.button.download') ?></a>
                <a class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" href="<?php print $this->page('clear') ?>"><?php print $this->modx->lexicon('log404.button.clear') ?></a>
            </div>
        </div>

        <?php if (!empty($this->error)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach($this->error as $error) : ?>
                <p><?php print $error ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="modx-log-404-table mt-5 x-panel main-wrapper x-grid3-header">
            <table id="Datatable" class="table">
                <thead class="thead-dark">
                    <tr>
                    <?php foreach ($header as $value) : ?>
                        <th scope="col"><strong><?php echo $value; ?></strong></th>
                    <?php endforeach; ?>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $row) : ?>
                        <tr>
                            <?php foreach ($row as $value) : ?>
                                <td><?php echo $value; ?></td>
                            <?php endforeach; ?>
                            <td><button class="btn btn-danger" data-action="<?php print $this->page('delete') ?>" data-url="<?php print $row['url'] ?>">x</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>