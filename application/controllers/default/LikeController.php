<?php
require_once ('application/controllers/default/AbstractController.php');

/**
 * 
 * @author Ivan
 *
 */
class LikeController extends AbstractController
{
    
    /**
     * 點讚
     * 
     */
    public function likeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNeverRender();

        $data = $this->_toggleLike(LIKE, $this->getParam('boardSN'), Orbas_Auth::getUserInfo('SN', ROLE_MEMBER));
        $this->_helper->json((array)$data);
    }
    
    /**
     * 誰點讚
     * 
     */
    public function whosLikeAction()
    {
        $this->_helper->json(
	       $this->getModel()->fetchLikeByBoard($this->getParam('board_sn'))
        );
    }
    
    public function whosDislikeAction()
    {
        $this->_helper->json(
	       $this->getModel()->fetchDislikeByBoard($this->getParam('board_sn'))
        );
    }
    
    /**
     * 點爛
     * 
     */
    public function dislikeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNeverRender();
        
        $data = $this->_toggleLike(DISLIKE, $this->getParam('boardSN'), Orbas_Auth::getUserInfo('SN', ROLE_MEMBER));
        $this->_helper->json((array)$data);
    }
    
    /**
     * 切換讚或爛
     * 
     * @param integer $type
     * @param integer $boardSN
     * @param integer $user
     * 
     * @return stdClass
     */
    protected function _toggleLike($type, $boardSN, $user)
    {
        $model = $this->getModel();
        $row = $model->fetchRow('USER_SN = ' . $user . ' AND TYPE_KEY = ' . $type . ' AND BOARD_SN = ' . $boardSN);
        
        $class = new stdClass();
        if($row) {
            $row->delete();
            $class->on = 0;
        } else {
            $model->append(array(
            	'USER_SN'  => $user,
                'TYPE_KEY' => $type,
                'BOARD_SN' => $this->getParam('boardSN')
            ));
            $class->on = 1;
        }
        
        $class->count = $model->fetchCountByBoard($this->getParam('boardSN'), $type);

        return $class;
    }
}
?>