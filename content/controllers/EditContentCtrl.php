<?php


use Core\Config;
use Core\Management\Controller;
use Core\Management\Request;
use Core\Management\Response;
use Core\ORM\Repository;
use modules\block\models\VersionContent;
use modules\block\repositories\BlockRepository;

class EditContentCtrl extends Controller{

    protected $path = 'content/edit/*';
    protected $access = \modules\content\accesses\EditContentAcs::class;

    public function stand(Request $request, Response $response){
        $fileRepo = new \modules\content\repositories\FileRepository();
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> createContentRepo()
            -> one('contents.id', 'contents.type', 'contents.block_id', 'contents.access_needed', 'version_contents.id AS vid', 'version_contents.content', 'version_contents.title', 'version_contents.importance', 'version_contents.current')
            -> where('contents.id', equals(getUrl(2)))
            -> join('version_contents', 'version_contents.content_id = contents.id AND version_contents.current = 1')
            -> where('contents.active', equals(1))
            -> belongsEntity('version_contents_id', $fileRepo -> get(), 'vid')
            -> go();
        $block = $blockRepo -> one() -> where('id', equals($content -> block_id)) -> go();
        $blocks = $blockRepo -> get('blocks.name', 'blocks.id')
            -> where('active', equals(1))
            -> join('grants', 'grants.block_id = blocks.id AND grants.privilege_id = 11381218 AND grants.user_id = '.$response -> getSession('user')['id'])
            -> go();
        $parentBlocks = $blockRepo -> getAvailableBlocksForAddContent($response -> getSession('user')['id'], $content -> access_needed);
        return $response -> page('addContent', ['content' => $content, 'type' => $content -> type, 'blocks' => $blocks, 'block' => $block, 'parentBlocks' => $parentBlocks]);
    }

    public function action(Request $request, Response $response){
        $version_content = new VersionContent();
        $version_content -> content_id = getUrl(2);

        $version_content -> content = $request -> getPostParam('content');
        if ($version_content -> content == ' ' || $version_content -> content == '  ')
            $version_content -> content = '//';
        $version_content -> title = $request -> getPostParam('title');
        $version_content -> importance = $request -> getPostParam('importance');
        $version_content -> user_id = $response -> getSession('user')['id'];
        $version_content -> current = 1;
        $blockRepo = new BlockRepository();
        $content = $blockRepo -> createContentRepo()
            -> one()
            -> where('id', equals(getUrl(2)))
            -> go();
        $content -> access_needed = $request -> getPostParam('access_needed');
        if ($request -> getPostParam('block_id') != '')
            $content -> block_id = $request -> getPostParam('block_id');
        $eventRepo = new \modules\main\repositories\EventRepository();
        $eventRepo -> createEntityEvent('edit', $content -> getTable(), $content -> block_id, $content -> id);
        if ($version_content -> isValid()){
            $mapper = Config::getMapper('main');
            $repo = new Repository(new VersionContent());
            $repo -> upd() -> set('current', 0) -> where('content_id', equals(getUrl(2))) -> where('current', equals(1)) -> go();
            $mapper -> insert($version_content);
            $mapper -> update($content);
            $fileRepo = new \modules\content\repositories\FileRepository();
            $fileRepo -> saveFiles($version_content -> id);
            $fileRepo -> insertLastFiles($version_content -> id);
            //header('Location: /block/show/'.$content -> block_id);
        }
    }

    public function forbidden(Request $request, Response $response){
        return $response -> page('403-2');
    }
}