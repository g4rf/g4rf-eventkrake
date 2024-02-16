import { useRef, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import * as List from './list-events';
import * as Controls from './controls';

export default function Edit({ attributes, setAttributes }) 
{
    const { showContent } = attributes;
 
    // load events
    const list = useRef(null);
    useEffect(() => {
        List.load({
            block: list.current,
            isEditor: true
        });
    });
    
    return (
        <div { ...useBlockProps() } ref={list} >
            
            <InspectorControls>
                <PanelBody title={ __( 'Elements', 'g4rf-eventkrake' ) }>
                    <ToggleControl
                        checked={ !! showContent }
                        label={ __('Show content', 'g4rf-eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showContent: ! showContent,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                </PanelBody>
            </InspectorControls>
            
            <List.html attributes={attributes} />
            
        </div>
    )
}
