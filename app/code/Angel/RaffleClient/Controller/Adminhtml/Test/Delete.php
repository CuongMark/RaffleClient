<?php


namespace Angel\RaffleClient\Controller\Adminhtml\Test;

class Delete extends \Angel\RaffleClient\Controller\Adminhtml\Test
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('test_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Angel\RaffleClient\Model\Test::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Test.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['test_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Test to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
