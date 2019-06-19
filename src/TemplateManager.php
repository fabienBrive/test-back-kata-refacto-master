<?php

class TemplateManager
{
    /**
     * principal function replace array data by Quote object
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
     * call by getTemplateComputed for subject and content, global refacto (property name, indentation, structur)
     * pass in parameter the Quote Object directly
     */
    private function computeText($text, Quote $quote)
    {
        // Récupération context (currentSite, currentUser)
        $applicationContext = ApplicationContext::getInstance();

        if ($quote)
        {
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            $user = $applicationContext->getCurrentUser();

            // treatement for Summury
            if (strpos($text, '[quote:summary_html]') !== false || strpos($text, '[quote:summary]') !== false) {
                
                if ($containsSummaryHtml !== false) {
                    $text = str_replace('[quote:summary_html]', Quote::renderHtml($_quoteFromRepository), $text);
                }

                if ($containsSummary !== false) {
                    $text = str_replace('[quote:summary]', Quote::renderText($_quoteFromRepository), $text);
                }
            }

            // treatement for Destination
            if (strpos($text, '[quote:destination_name]') !== false) {
                $detsinationText = $destination->conjunction .' '. $destination->countryName;
                $text = str_replace('[quote:destination_name]',$detsinationText, $text);
            }

            if (isset($destination)){
                $text = str_replace('[quote:destination_link]', $site->url . '/' . $destination->countryName . '//quote/' . $quote->id, $text);
            } else {
                $text = str_replace('[quote:destination_link]', '', $text);
            }
        }
         // treatment for User
        if(isset($user)) {
            (strpos($text, '[user:first_name]') !== false) 
            and 
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
