<?php

namespace App\Controller;
use MongoDB;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportsController extends AbstractController
{

    /**
     * @Route("/reports", name="app_reports")
     */
    public function index(): Response
    {
        return $this->render('reports/index.html.twig', [
            'controller_name' => 'ReportsController',
        ]);
    }

     /**
     * @Route("/get_data", name="get_data")
     * 
     */
    public function data( Request $request)
    {
        $r= $request->query->get('tipo');
        $id_account= $request->query->get('id_account');

        $collection = (new MongoDB\Client)->demo_db->accounts;

        if($r==-1)//Recupera todos los usuarios
           $cursor = $collection->find(['status' => 'ACTIVE']);
        else
           $cursor = $collection->find(['accountId' => $id_account, 'status' => 'ACTIVE']);

    
        $pila = array();
        foreach ($cursor as $document) {
       
            $collection2 = (new MongoDB\Client)->demo_db->metrics;
            $cursor2 = $collection2->find(['accountId' => $document['accountId']]);

            $spend=0;
            $clicks=0;
            $impressions=0;
            $costPerClick=0;
            
            foreach ($cursor2 as $document2) {

                if ( isset($document2['spend']))
                  $spend+=floatval($document2['spend']) ;
                if ( isset($document2['clicks']))
                  $clicks+=floatval($document2['clicks']);
                if ( isset($document2['impressions']))
                  $impressions+=floatval($document2['impressions']);
                if ( isset($document2['costPerClick']))
                {$string = str_replace ( ',', '.', $document2['costPerClick']);
                $costPerClick+=floatval($string);}
              
            }

            if($clicks>0)
              $avg=$costPerClick/$clicks;
            else
              $avg=0;
           
            $obj = (object) array(
                'accountName' => $document['accountName'],
                'accountId' => $document['accountId'],
                'spend' => $spend,
                'clicks' => $clicks,
                'impressions' => $impressions,
                'costPerClick' =>  $avg,
            );

            array_push($pila, $obj);
        }

    
        $response = new JsonResponse([
            'datos' => $pila,
        ]);
        
        //  Use the JSON_PRETTY_PRINT 
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        
        return $response;
        
    }


}
