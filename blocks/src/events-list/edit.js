import { useRef, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import * as List from './list-events';
import * as Controls from './controls';

export default function Edit({ attributes, setAttributes }) 
{
    const {
        dateStart,
        dateEnd,
        showImage,
        showTitle,
        showExcerpt,
        showContent,
        showSeperator,
        showDate,
        showDateStart,
        showDateEnd,
        showDateIcs,
        showLocation,
        showLocationWithLink,
        showLocationAddress
    } = attributes;
 
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
                <PanelBody title={ __( 'Date Range', 'eventkrake' ) }>
                    {/* date from */}
                    <TextControl
                        label={ __('Start date', 'eventkrake') }
                        value={ dateStart }
                        onChange={ ( value ) => {
                            setAttributes( {
                                dateStart: value,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            }); 
                        }}
                    />
                    {/* date to */}
                    <TextControl
                        label={ __('End date', 'eventkrake') }
                        value={ dateEnd }
                        onChange={ ( value ) => {
                            setAttributes( {
                                dateEnd: value,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            }); 
                        }}
                    />
                </PanelBody>
                
                <PanelBody title={ __( 'Show', 'eventkrake' ) }>
                    {/* title */}
                    <ToggleControl
                        checked={ !! showTitle }
                        label={ __('Show title', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showTitle: ! showTitle,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* excerpt */}
                    <ToggleControl
                        checked={ !! showExcerpt }
                        label={ __('Show excerpt', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showExcerpt: ! showExcerpt,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* content */}
                    <ToggleControl
                        checked={ !! showContent }
                        label={ __('Show content', 'eventkrake') }
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
                
                <PanelBody title={ __( 'Date', 'eventkrake' ) }>
                    {/* date */}
                    <ToggleControl
                        checked={ !! showDate }
                        label={ __('Show date', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showDate: ! showDate,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* start date */}
                    <ToggleControl
                        checked={ !! showDateStart }
                        label={ __('Show start date', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showDateStart: ! showDateStart,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* end date */}
                    <ToggleControl
                        checked={ !! showDateEnd }
                        label={ __('Show end date', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showDateEnd: ! showDateEnd,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* date ics */}
                    <ToggleControl
                        checked={ !! showDateIcs }
                        label={ __('Show ics link', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showDateIcs: ! showDateIcs,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                </PanelBody>
                
                <PanelBody title={ __( 'Location', 'eventkrake' ) }>
                    {/* location */}
                    <ToggleControl
                        checked={ !! showLocation }
                        label={ __('Show location', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showLocation: ! showLocation,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* location link */}
                    <ToggleControl
                        checked={ !! showLocationWithLink }
                        label={ __('Link location to location page', 
                                                        'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showLocationWithLink: ! showLocationWithLink,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                    {/* location address */}
                    <ToggleControl
                        checked={ !! showLocationAddress }
                        label={ __('Show location address', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showLocationAddress: ! showLocationAddress,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                </PanelBody>
                
                <PanelBody title={ __( 'Image', 'eventkrake' ) }>
                    {/* image */}
                    <ToggleControl
                        checked={ !! showImage }
                        label={ __('Show image', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showImage: ! showImage,
                            } );
                            List.load({
                                block: list.current,
                                isEditor: true
                            });
                        }}
                    />
                </PanelBody>
                
                <PanelBody title={ __( 'Seperator', 'eventkrake' ) }>

                    {/* seperator */}
                    <ToggleControl
                        checked={ !! showSeperator }
                        label={ __('Show event seperator', 'eventkrake') }
                        onChange={ () => {
                            setAttributes( {
                                showSeperator: ! showSeperator,
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
