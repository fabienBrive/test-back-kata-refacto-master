<?php

class TemplateManager
{
    /**
     * principal function pourquoi un array pour quote alors qu'on ne passe qu'un objet quote dedans??
     */
    public function getTemplateComputed(Template $tpl, Quote $quote)
    {

        // passer un try catch pour l'exception arréter le script si pas de template
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $quote);
        $replaced->content = $this->computeText($replaced->content, $quote);

        return $replaced;
    }


    /**
     * appelé dans getTemplateComputed, même remarque pour le tableau de quote qui ne contien que Quote
     */
    private function computeText($text, Quote $quote)
    {
        // Récupération context (currentSite, currentUser)
        $applicationContext = ApplicationContext::getInstance();

        if ($quote)
        {
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            var_dump($destination);
            $user = $applicationContext->getCurrentUser();


            if (strpos($text, '[quote:summary_html]') !== false || strpos($text, '[quote:summary]') !== false) {
                
                if ($containsSummaryHtml !== false) {
                    $text = str_replace('[quote:summary_html]', Quote::renderHtml($_quoteFromRepository), $text);
                }

                if ($containsSummary !== false) {
                    $text = str_replace('[quote:summary]', Quote::renderText($_quoteFromRepository), $text);
                }
            }

            if (strpos($text, '[quote:destination_name]') !== false) {
                $detsinationText = $destination->conjunction .' '. $destination->countryName;
                var_dump($detsinationText);
                $text = str_replace('[quote:destination_name]',$detsinationText, $text);
            }
        }

        
        if (isset($destination)){
            $text = str_replace('[quote:destination_link]', $site->url . '/' . $destination->countryName . '//quote/' . $quote->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }
            

        
        if(isset($user)) {
            (strpos($text, '[user:first_name]') !== false) 
            and 
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
