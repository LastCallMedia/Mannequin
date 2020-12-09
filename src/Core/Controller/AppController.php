<?php

    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\Framework\Controller\Controller;


    class AppController extends Controller{
        /**
         * @Route ("/")
         *
         */
        public function index() {
//            return new Response('<html><body></body></html>');

            return $this->render('');

        }
}
